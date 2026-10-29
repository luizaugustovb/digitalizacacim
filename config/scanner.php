<?php

return [
    'protocol' => env('SCANNER_AGENT_PROTOCOL', 'digitalizacacim-scanner'),
    'session_minutes' => (int) env('SCANNER_SESSION_MINUTES', 5),
    'agent_download_url' => env(
        'SCANNER_AGENT_DOWNLOAD_URL',
        'https://github.com/luizaugustovb/digitalizacacim/releases/latest/download/DigitalizacaoCacim.ScannerAgent.exe'
    ),
    'agent_download_url_x86' => env(
        'SCANNER_AGENT_DOWNLOAD_URL_X86',
        'https://github.com/luizaugustovb/digitalizacacim/releases/latest/download/DigitalizacaoCacim.ScannerAgent-x86.exe'
    ),
];
