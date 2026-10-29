# Agente Windows de Digitalização

Este agente substitui o componente Asprise no fluxo de digitalização do sistema. Ele usa a interface TWAIN do driver instalado no Windows, abre a janela nativa do scanner e envia o arquivo diretamente ao pedido e tipo de documento escolhidos no sistema.

## Requisitos da estação

- Windows 10 ou Windows 11 de 64 bits.
- Driver TWAIN/ISIS do scanner instalado. O Canon imageFORMULA P-208II utiliza este driver.
- .NET 8 Desktop Runtime x64.
- A aplicação web deve ser acessada por HTTPS em produção. O agente só aceita envio HTTP para `localhost`, impedindo o envio de documentos e autorizações temporárias por uma rede não criptografada.

O piloto gera um PNG por operação de digitalização. O driver pode apresentar sua própria tela para escolher origem, resolução e alimentador automático.

## Publicação e instalação

Na máquina de desenvolvimento:

```powershell
cd scanner-agent
.\publish-agent.ps1 -Runtime win-x64
.\publish-agent.ps1 -Runtime win-x86
```

Publique `publish/win-x64/DigitalizacaoCacim.ScannerAgent.exe` e `publish/win-x86/DigitalizacaoCacim.ScannerAgent.exe` como assets de release. A tela **Configurações > Scanner** oferece as duas edições.

Na estação, baixe e dê duplo clique na edição que corresponde ao driver TWAIN. Use 64 bits por padrão; use a edição 32 bits quando o scanner aparecer no software do fabricante, mas não aparecer no agente 64 bits. O próprio executável:

- copia-se para `%LocalAppData%\DigitalizacaoCACIM\ScannerAgent`;
- registra o protocolo `digitalizacacim-scanner://` para o usuário atual;
- não exige instalação do .NET.

Também é possível executar:

```powershell
.\DigitalizacaoCacim.ScannerAgent.exe --install
```

## Uso

1. Abra um pedido pendente e escolha **Escanear pelo Windows** no documento desejado.
2. Autorize o navegador a abrir o Agente de Digitalização CACIM, se solicitado.
3. Na janela do Windows, selecione o scanner e digitalize.
4. O agente anexa o PNG ao pedido e a página atualiza automaticamente.

Cada operação usa uma autorização única válida por cinco minutos. O agente não pode escolher outro pedido nem outro tipo de documento.

## Diagnóstico

Se a janela do scanner não abrir:

1. Confirme que o Canon aparece no seletor do driver TWAIN/CaptureOnTouch.
2. Reinstale/atualize o driver ISIS/TWAIN do fabricante.
3. Execute novamente `DigitalizacaoCacim.ScannerAgent.exe --install`.
4. Confirme que o sistema está aberto por HTTPS e que a estação tem acesso ao servidor.

O upload manual por **Selecionar Arquivo** continua disponível se o scanner estiver indisponível.
