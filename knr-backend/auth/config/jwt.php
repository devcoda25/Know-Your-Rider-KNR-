<?php

return [

    'secret' => env('JWT_SECRET'),

    'issuer' => env('APP_URL', 'http://localhost:8001'),

    // Single access token for now. Add refresh tokens before production.
    'ttl_minutes' => (int) env('JWT_TTL_MINUTES', 720),

];
