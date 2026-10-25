<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // OTP and PIN login: slow down guessing and SMS abuse.
        RateLimiter::for('otp', fn (Request $request) => [
            Limit::perMinute(5)->by($request->ip()),
            Limit::perHour(20)->by((string) $request->input('phone', $request->ip())),
        ]);

        // Public QR / plate lookups: stops bulk scraping of the rider registry.
        RateLimiter::for('public-lookup', fn (Request $request) => [
            Limit::perMinute(20)->by($request->ip()),
            Limit::perHour(120)->by($request->ip()),
        ]);

        // Photos on the public scan page: a few images per page view.
        RateLimiter::for('public-files', fn (Request $request) => Limit::perMinute(120)->by($request->ip()));

        // Everything else behind a token.
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(120)->by(
            data_get($request->attributes->get('gateway.user'), 'id') ?? $request->ip()
        ));
    }
}
