<?php

return [
    'protocol' => env('SCANNER_AGENT_PROTOCOL', 'digitalizacacim-scanner'),
    'session_minutes' => (int) env('SCANNER_SESSION_MINUTES', 5),
    'agent_download_url' => env(
        'SCANNER_AGENT_DOWNLOAD_URL',
        'https://github.com/luizaugustovb/digitalizacacim/releases/latest/download/DigitalizacaoCacim.ScannerAgent.exe'
    ),
];
