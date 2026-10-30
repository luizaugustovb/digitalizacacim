using System.Net.Http.Headers;
using System.Net;
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
    private const string ExecutableName = "DigitalizacaoCacim.ScannerAgent.exe";
    private const string SettingsRegistryPath = @"Software\DigitalizacaoCACIM\ScannerAgent";

    [STAThread]
    private static async Task Main(string[] args)
    {
        ApplicationConfiguration.Initialize();

        if (IsInstallCommand(args, out var allowInsecureHttp))
        {
            try
            {
                InstallAgent(allowInsecureHttp);
                MessageBox.Show(
                    allowInsecureHttp
                        ? "Agente instalado com HTTP permitido para endereços privados da rede."
                        : "Agente de digitalização instalado para o usuário atual.",
                    "Digitalização CACIM",
                    MessageBoxButtons.OK,
                    MessageBoxIcon.Information);
            }
            catch (Exception exception)
            {
                MessageBox.Show(
                    $"Não foi possível instalar o agente.\n\n{exception.Message}",
                    "Digitalização CACIM",
                    MessageBoxButtons.OK,
                    MessageBoxIcon.Error);
            }

            return;
        }

        ScanRequest? request = null;
        string? error = null;

        if (args.Length != 1 || !ScanRequest.TryParse(args[0], IsInsecureHttpAllowed(), out request, out error))
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
            AgentDiagnostics.Log("Falha na digitalização", exception);
            MessageBox.Show(
                $"Não foi possível digitalizar o documento.\n\n{exception.GetBaseException().Message}\n\nDiagnóstico: {AgentDiagnostics.LogPath}",
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

    private static bool IsInstallCommand(string[] args, out bool allowInsecureHttp)
    {
        allowInsecureHttp = false;

        if (args.Length == 0 || (args.Length == 1 && string.Equals(args[0], "--install", StringComparison.OrdinalIgnoreCase)))
        {
            return true;
        }

        if (args.Length == 2
            && string.Equals(args[0], "--install", StringComparison.OrdinalIgnoreCase)
            && string.Equals(args[1], "--allow-insecure-http", StringComparison.OrdinalIgnoreCase))
        {
            allowInsecureHttp = true;
            return true;
        }

        return false;
    }

    private static void InstallAgent(bool allowInsecureHttp)
    {
        var source = Environment.ProcessPath
            ?? throw new InvalidOperationException("Não foi possível identificar o executável do agente.");
        var installationDirectory = Path.Combine(
            Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData),
            "DigitalizacaoCACIM",
            "ScannerAgent");
        var destination = Path.Combine(installationDirectory, ExecutableName);

        Directory.CreateDirectory(installationDirectory);

        if (!string.Equals(source, destination, StringComparison.OrdinalIgnoreCase))
        {
            try
            {
                File.Copy(source, destination, true);
            }
            catch (IOException exception)
            {
                throw new InvalidOperationException(
                    "Feche qualquer janela do Agente de Digitalização CACIM e execute este instalador novamente.",
                    exception);
            }
        }

        RegisterProtocol(destination);
        using var settings = Registry.CurrentUser.CreateSubKey(SettingsRegistryPath)
            ?? throw new InvalidOperationException("Não foi possível salvar as configurações do agente.");
        settings.SetValue("AllowInsecureHttp", allowInsecureHttp ? 1 : 0, RegistryValueKind.DWord);
    }

    private static bool IsInsecureHttpAllowed()
    {
        using var settings = Registry.CurrentUser.OpenSubKey(SettingsRegistryPath);
        return settings?.GetValue("AllowInsecureHttp") is 1;
    }

    private static void RegisterProtocol(string executable)
    {

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
    public static bool TryParse(string value, bool allowInsecureHttp, out ScanRequest? request, out string? error)
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
            || !IsAllowedUploadUri(uploadUri, allowInsecureHttp))
        {
            error = "A solicitação de digitalização não contém uma autorização válida.";
            return false;
        }

        request = new ScanRequest(sessionId, token, uploadUri);
        return true;
    }

    private static bool IsAllowedUploadUri(Uri uploadUri, bool allowInsecureHttp)
    {
        if (uploadUri.Scheme == Uri.UriSchemeHttps || uploadUri.IsLoopback)
        {
            return true;
        }

        if (!allowInsecureHttp || uploadUri.Scheme != Uri.UriSchemeHttp || !IPAddress.TryParse(uploadUri.Host, out var address))
        {
            return false;
        }

        var bytes = address.GetAddressBytes();

        return bytes.Length == 4
            && (bytes[0] == 10
                || (bytes[0] == 172 && bytes[1] is >= 16 and <= 31)
                || (bytes[0] == 192 && bytes[1] == 168));
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
internal static class AgentDiagnostics
{
    public static string LogPath => Path.Combine(
        Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData),
        "DigitalizacaoCACIM",
        "ScannerAgent",
        "diagnostico.log");

    public static void Log(string message, Exception? exception = null)
    {
        Directory.CreateDirectory(Path.GetDirectoryName(LogPath)!);

        var details = $"{DateTimeOffset.Now:O} | {message} | Processo {(Environment.Is64BitProcess ? "x64" : "x86")}";
        if (exception is not null) {
            details += Environment.NewLine + exception;
        }

        File.AppendAllText(LogPath, details + Environment.NewLine + Environment.NewLine);
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
                var applicationIdentity = TWIdentity.Create(
                    DataGroups.Image | DataGroups.Control,
                    new Version(1, 0),
                    "CACIM",
                    "Digitalização CACIM",
                    "Agente de Digitalização CACIM",
                    "Agente Windows para scanners TWAIN"
                );
                _session = new TwainSession(applicationIdentity);
                _session.DataTransferred += OnDataTransferred;
                _session.SourceDisabled += OnSourceDisabled;
                _session.TransferError += (_, _) => CompleteWithError(
                    new InvalidOperationException("O driver TWAIN não conseguiu transferir a imagem."));
                _session.TransferCanceled += (_, _) => Close();

                _messageLoopHook = new WindowsFormsMessageLoopHook(Handle);
                _session.Open(_messageLoopHook);

                var sources = _session.GetSources().Select(source => source.Name).ToArray();
                AgentDiagnostics.Log($"Fontes TWAIN encontradas: {string.Join(", ", sources)}");

                if (sources.Length == 0)
                {
                    throw new InvalidOperationException(
                        "Nenhuma fonte TWAIN foi encontrada para esta arquitetura do agente.");
                }

                var source = _session.ShowSourceSelector();
                if (source is null)
                {
                    Close();
                    return;
                }

                source.Open();
                source.Enable(SourceEnableMode.ShowUI, true, Handle);
            }
            catch (Exception exception)
            {
                AgentDiagnostics.Log("Falha ao abrir fonte TWAIN", exception);
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
