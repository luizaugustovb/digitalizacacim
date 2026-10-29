<?php

return [
    'protocol' => env('SCANNER_AGENT_PROTOCOL', 'digitalizacacim-scanner'),
    'session_minutes' => (int) env('SCANNER_SESSION_MINUTES', 5),
];
