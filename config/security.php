<?php

return [
    // Aktifkan pada hosting HTTPS. Untuk localhost boleh false agar mudah debug.
    'csp' => env('SECURITY_CSP', false),
    'force_https' => env('FORCE_HTTPS', false),
];
