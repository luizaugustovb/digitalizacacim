using System.Net.Http.Headers;
using System.Runtime.Versioning;
using System.Drawing.Imaging;
using Microsoft.Win32;
using NTwain;
using NTwain.Data;

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
            temporaryFile = TwainScanner.AcquirePng();
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
internal static class TwainScanner
{
    public static string AcquirePng()
    {
        using var form = new TwainAcquisitionForm();
        Application.Run(form);

        return form.GetResult();
    }

    private sealed class TwainAcquisitionForm : Form
    {
        private TwainSession? _session;
        private WindowsFormsMessageLoopHook? _messageLoopHook;
        private string? _filePath;
        private Exception? _error;

        public TwainAcquisitionForm()
        {
            Opacity = 0;
            ShowInTaskbar = false;
            StartPosition = FormStartPosition.Manual;
            Location = new Point(-32000, -32000);
        }

        public string GetResult()
        {
            if (_error is not null)
            {
                throw _error;
            }

            return _filePath ?? throw new OperationCanceledException();
        }

        protected override void OnShown(EventArgs e)
        {
            base.OnShown(e);
            BeginInvoke(StartScan);
        }

        protected override void OnFormClosed(FormClosedEventArgs e)
        {
            try
            {
                if (_session?.IsSourceOpen == true)
                {
                    _session.CurrentSource.Close();
                }

                if (_session?.IsDsmOpen == true)
                {
                    _session.Close();
                }
            }
            catch
            {
                // The driver can already have closed the source or manager.
            }
            base.OnFormClosed(e);
        }

        private void StartScan()
        {
            try
            {
                _session = new TwainSession(DataGroups.Image | DataGroups.Control);
                _session.DataTransferred += OnDataTransferred;
                _session.SourceDisabled += OnSourceDisabled;
                _session.TransferError += (_, _) => CompleteWithError(
                    new InvalidOperationException("O driver TWAIN não conseguiu transferir a imagem."));
                _session.TransferCanceled += (_, _) => Close();

                _messageLoopHook = new WindowsFormsMessageLoopHook(Handle);
                _session.Open(_messageLoopHook);

                var source = _session.ShowSourceSelector();
                if (source is null)
                {
                    Close();
                    return;
                }

                source.Enable(SourceEnableMode.ShowUI, true, Handle);
            }
            catch (Exception exception)
            {
                CompleteWithError(new InvalidOperationException(
                    "Não foi possível abrir um driver TWAIN. Confirme que o driver ISIS/TWAIN da Canon está instalado.",
                    exception));
            }
        }

        private void OnDataTransferred(object? sender, DataTransferredEventArgs eventArgs)
        {
            try
            {
                using var source = eventArgs.GetNativeImageStream();
                using var image = Image.FromStream(source);

                _filePath = Path.Combine(Path.GetTempPath(), $"digitalizacacim-{Guid.NewGuid():N}.png");
                image.Save(_filePath, ImageFormat.Png);
            }
            catch (Exception exception)
            {
                CompleteWithError(new InvalidOperationException(
                    "O driver TWAIN retornou uma imagem que não pôde ser processada.",
                    exception));
            }
        }

        private void OnSourceDisabled(object? sender, EventArgs eventArgs)
        {
            Close();
        }

        private void CompleteWithError(Exception exception)
        {
            _error = exception;
            Close();
        }
    }
}
