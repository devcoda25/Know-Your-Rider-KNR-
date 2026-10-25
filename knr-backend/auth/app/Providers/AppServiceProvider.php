<?php

namespace App\Providers;

use App\Services\Sms\LogSmsSender;
use App\Services\Sms\SmsSender;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Add real SMS drivers (Africa's Talking, etc.) here keyed by config('otp.sms_driver').
        $this->app->bind(SmsSender::class, fn () => match (config('otp.sms_driver')) {
            default => new LogSmsSender,
        });
    }

    public function boot(): void
    {
        //
    }
}
