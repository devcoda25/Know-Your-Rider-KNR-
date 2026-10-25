<?php

use App\Http\Controllers\InternalRoleController;
use App\Http\Controllers\MeController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\PinController;
use Illuminate\Support\Facades\Route;

// Reached only through the gateway (see InternalKey middleware).
Route::prefix('auth')->group(function () {
    // Public (gateway skips the JWT check for these)
    Route::post('otp/request', [OtpController::class, 'request']);
    Route::post('otp/verify', [OtpController::class, 'verify']);
    Route::post('pin/login', [PinController::class, 'login']);

    // Protected (gateway verified the JWT and sent X-User-Id)
    Route::post('pin', [PinController::class, 'set']);
    Route::get('me', MeController::class);
});

// Service-to-service. Not listed in the gateway, so clients can never reach it.
Route::post('internal/role-assignments', [InternalRoleController::class, 'assign']);
