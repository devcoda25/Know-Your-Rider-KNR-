<?php

use App\Http\Controllers\ProxyController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

$proxy = [ProxyController::class, 'forward'];

Route::prefix('v1')->group(function () use ($proxy) {

    // Gateway + upstream health, used by uptime checks and the admin app.
    Route::get('health', function () {
        $services = [];
        foreach (config('gateway.upstreams') as $name => $url) {
            try {
                $services[$name] = Http::timeout(2)->get(rtrim($url, '/').'/up')->successful() ? 'ok' : 'down';
            } catch (\Throwable) {
                $services[$name] = 'down';
            }
        }

        return ['service' => 'gateway', 'status' => 'ok', 'upstreams' => $services];
    });

    // ---- Public routes (no token) ----------------------------------------------------

    Route::middleware('throttle:otp')->group(function () use ($proxy) {
        Route::post('auth/otp/{action}', $proxy)
            ->where('action', 'request|verify')
            ->defaults('upstream', 'auth')->defaults('resource', 'auth/otp');

        Route::post('auth/pin/login', $proxy)
            ->defaults('upstream', 'auth')->defaults('resource', 'auth/pin/login');
    });

    // Photos shown on the public scan page.
    Route::middleware('throttle:public-files')->get('public/files/{path}', $proxy)
        ->where('path', '.*')
        ->defaults('upstream', 'registry')->defaults('resource', 'public/files');

    // Passenger QR scan, plate search and reports. A valid token is optional: staff who
    // are signed in get the full profile, everyone else gets the public view.
    //   GET  /v1/public/riders/qr/{token}   GET /v1/public/riders/plate/{plate}
    //   POST /v1/public/riders/qr/{token}/report
    Route::middleware(['gateway.jwt.optional', 'throttle:public-lookup'])
        ->match(['GET', 'POST'], 'public/{path}', $proxy)
        ->where('path', '.*')
        ->defaults('upstream', 'registry')->defaults('resource', 'public');

    // ---- Protected routes (valid JWT required) -----------------------------------------

    Route::middleware(['gateway.jwt', 'throttle:api'])->group(function () use ($proxy) {
        // Only these two auth endpoints are public-facing for signed-in users.
        Route::get('auth/me', $proxy)->defaults('upstream', 'auth')->defaults('resource', 'auth/me');
        Route::post('auth/pin', $proxy)->defaults('upstream', 'auth')->defaults('resource', 'auth/pin');

        foreach (config('gateway.resources') as $resource => $upstream) {
            Route::any("$resource/{path?}", $proxy)
                ->where('path', '.*')
                ->defaults('upstream', $upstream)->defaults('resource', $resource);
        }
    });
});
