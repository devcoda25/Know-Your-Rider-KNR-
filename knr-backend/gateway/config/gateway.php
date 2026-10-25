<?php

return [

    // HS256 secret shared with the auth service, which signs the tokens.
    'jwt_secret' => env('JWT_SECRET'),

    // Sent to every upstream so they can reject traffic that did not come through the gateway.
    'internal_key' => env('INTERNAL_API_KEY'),

    'timeout' => (int) env('GATEWAY_TIMEOUT', 15),

    'upstreams' => [
        'auth' => env('AUTH_SERVICE_URL', 'http://auth:8000'),
        'registry' => env('REGISTRY_SERVICE_URL', 'http://registry:8000'),
    ],

    // Protected resources: first URL segment => upstream service.
    // The auth service is NOT listed: only /auth/me and /auth/pin are exposed (see routes/api.php),
    // so its internal endpoints can never be reached from outside.
    // Add a line here when a new resource is exposed through the gateway.
    'resources' => [
        'stages' => 'registry',
        'riders' => 'registry',
        'bikes' => 'registry',
        'incidents' => 'registry',
    ],

];
