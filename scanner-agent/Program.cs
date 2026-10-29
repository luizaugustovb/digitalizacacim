using System.Net.Http.Headers;
using System.Runtime.Versioning;
using Microsoft.Win32;

namespace DigitalizacaoCacim.ScannerAgent;

[SupportedOSPlatform("windows")]
internal static class Program
{
    private const string Protocol = "digitalizacacim-scanner";

    [STAThread]
    private static async Task Main(string[] args)
    {
        ApplicationConfiguration.Initialize();

        if (args.Length == 1 && string.Equals(args[0], "--install", StringComparison.OrdinalIgnoreCase))
        {
            RegisterProtocol();
            MessageBox.Show(
                "Agente de digitalização instalado para o usuário atual.",
                "Digitalização CACIM",
                MessageBoxButtons.OK,
                MessageBoxIcon.Information);
            return;
        }

        ScanRequest? request = null;
        string? error = null;

        if (args.Length != 1 || !ScanRequest.TryParse(args[0], out request, out error))
        {
            MessageBox.Show(
                error ?? "Abra a digitalização pelo sistema CACIM.",
                "Digitalização CACIM",
                MessageBoxButtons.OK,
                MessageBoxIcon.Warning);
            return;
        }

        string? temporaryFile = null;

        try
        {
            temporaryFile = WiaScanner.AcquirePng();
            await UploadAsync(request!, temporaryFile);

            MessageBox.Show(
                "Documento digitalizado e anexado com sucesso.",
                "Digitalização CACIM",
                MessageBoxButtons.OK,
                MessageBoxIcon.Information);
        }
        catch (OperationCanceledException)
        {
            MessageBox.Show(
                "Digitalização cancelada.",
                "Digitalização CACIM",
                MessageBoxButtons.OK,
                MessageBoxIcon.Information);
        }
        catch (Exception exception)
        {
            MessageBox.Show(
                $"Não foi possível digitalizar o documento.\n\n{exception.Message}",
                "Digitalização CACIM",
                MessageBoxButtons.OK,
                MessageBoxIcon.Error);
        }
        finally
        {
            if (temporaryFile is not null && File.Exists(temporaryFile))
            {
                File.Delete(temporaryFile);
            }
        }
    }

    private static void RegisterProtocol()
    {
        var executable = Environment.ProcessPath
            ?? throw new InvalidOperationException("Não foi possível identificar o executável do agente.");

        using var key = Registry.CurrentUser.CreateSubKey($@"Software\Classes\{Protocol}")
            ?? throw new InvalidOperationException("Não foi possível registrar o protocolo do agente.");

        key.SetValue(string.Empty, "URL:Digitalização CACIM");
        key.SetValue("URL Protocol", string.Empty);

        using var command = key.CreateSubKey(@"shell\open\command")
            ?? throw new InvalidOperationException("Não foi possível registrar o comando do agente.");

        command.SetValue(string.Empty, $"\"{executable}\" \"%1\"");
    }

    private static async Task UploadAsync(ScanRequest request, string filePath)
    {
        using var client = new HttpClient { Timeout = TimeSpan.FromMinutes(2) };
        using var content = new MultipartFormDataContent();
        await using var fileStream = File.OpenRead(filePath);
        using var fileContent = new StreamContent(fileStream);

        fileContent.Headers.ContentType = new MediaTypeHeaderValue("image/png");
        content.Add(fileContent, "arquivo", Path.GetFileName(filePath));

        using var message = new HttpRequestMessage(HttpMethod.Post, request.UploadUrl)
        {
            Content = content,
        };
        message.Headers.Authorization = new AuthenticationHeaderValue("Bearer", request.Token);
        message.Headers.Accept.Add(new MediaTypeWithQualityHeaderValue("application/json"));

        using var response = await client.SendAsync(message);

        if (response.IsSuccessStatusCode)
        {
            return;
        }

        var details = await response.Content.ReadAsStringAsync();
        throw new InvalidOperationException(
            $"O sistema recusou o documento ({(int)response.StatusCode}). {details}");
    }
}

internal sealed record ScanRequest(string SessionId, string Token, Uri UploadUrl)
{
    public static bool TryParse(string value, out ScanRequest? request, out string? error)
    {
        request = null;
        error = null;

        if (!Uri.TryCreate(value, UriKind.Absolute, out var uri)
            || !string.Equals(uri.Scheme, "digitalizacacim-scanner", StringComparison.OrdinalIgnoreCase)
            || !string.Equals(uri.Host, "scan", StringComparison.OrdinalIgnoreCase))
        {
            error = "A solicitação de digitalização é inválida.";
            return false;
        }

        var parameters = ParseQuery(uri.Query);

        if (!parameters.TryGetValue("session", out var sessionId)
            || !Guid.TryParse(sessionId, out _)
            || !parameters.TryGetValue("token", out var token)
            || string.IsNullOrWhiteSpace(token)
            || !parameters.TryGetValue("upload_url", out var uploadUrl)
            || !Uri.TryCreate(uploadUrl, UriKind.Absolute, out var uploadUri)
            || (uploadUri.Scheme != Uri.UriSchemeHttps && !uploadUri.IsLoopback))
        {
            error = "A solicitação de digitalização não contém uma autorização válida.";
            return false;
        }

        request = new ScanRequest(sessionId, token, uploadUri);
        return true;
    }

    private static Dictionary<string, string> ParseQuery(string query)
    {
        return query.TrimStart('?')
            .Split('&', StringSplitOptions.RemoveEmptyEntries)
            .Select(part => part.Split('=', 2))
            .Where(pair => pair.Length == 2)
            .ToDictionary(
                pair => Uri.UnescapeDataString(pair[0]),
                pair => Uri.UnescapeDataString(pair[1]),
                StringComparer.OrdinalIgnoreCase);
    }
}

[SupportedOSPlatform("windows")]
internal static class WiaScanner
{
    private const string WiaPngFormat = "{B96B3CAF-0728-11D3-9D7B-0000F81EF32}";

    public static string AcquirePng()
    {
        var dialogType = Type.GetTypeFromProgID("WIA.CommonDialog")
            ?? throw new InvalidOperationException(
                "O Windows não encontrou o componente WIA. Instale o driver WIA do scanner e tente novamente.");

        dynamic dialog = Activator.CreateInstance(dialogType)
            ?? throw new InvalidOperationException("Não foi possível iniciar o diálogo do scanner Windows.");

        try
        {
            dynamic image = dialog.ShowAcquireImage(
                0,
                1,
                0,
                WiaPngFormat,
                true,
                true,
                false);

            if (image is null)
            {
                throw new OperationCanceledException();
            }

            var filePath = Path.Combine(Path.GetTempPath(), $"digitalizacacim-{Guid.NewGuid():N}.png");
            image.SaveFile(filePath);

            if (!File.Exists(filePath) || new FileInfo(filePath).Length == 0)
            {
                throw new InvalidOperationException("O scanner não retornou uma imagem válida.");
            }

            return filePath;
        }
        catch (System.Runtime.InteropServices.COMException exception) when ((uint)exception.HResult == 0x80210064)
        {
            throw new OperationCanceledException();
        }
    }
}
