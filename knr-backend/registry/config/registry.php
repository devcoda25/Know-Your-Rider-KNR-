<?php

return [

    // Base URL encoded in each rider's QR code (the public scan web page).
    'public_scan_url' => rtrim((string) env('PUBLIC_SCAN_URL', 'http://localhost:3000/r'), '/'),

    // Auth service (service-to-service calls use the shared internal key).
    'auth_url' => rtrim((string) env('AUTH_SERVICE_URL', 'http://auth:8000'), '/'),
    'internal_key' => env('INTERNAL_API_KEY'),

    // Public base URL of the gateway, used to build photo links returned to clients.
    'gateway_public_url' => rtrim((string) env('GATEWAY_PUBLIC_URL', 'http://localhost:8000/api/v1'), '/'),

    // Documents expiring within this many days show as "expiring soon".
    'expiring_soon_days' => 30,

    // Document types that must be valid for a rider to show as "verified".
    'required_documents' => ['permit', 'insurance'],

];
