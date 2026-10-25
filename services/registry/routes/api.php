<?php

use App\Http\Controllers\BikeController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\PublicRiderController;
use App\Http\Controllers\RiderController;
use App\Http\Controllers\StageController;
use Illuminate\Support\Facades\Route;

// Reached only through the gateway (see InternalKey middleware).

// ---- Public (no login). The gateway rate-limits these. -------------------------------
Route::prefix('public')->group(function () {
    Route::get('riders/qr/{token}', [PublicRiderController::class, 'byQr']);
    Route::get('riders/plate/{plate}', [PublicRiderController::class, 'byPlate']);
    Route::post('riders/qr/{token}/report', [PublicRiderController::class, 'report']);
    Route::get('files/{path}', FileController::class)->where('path', '.*');
});

// ---- Signed in (gateway verified the JWT and sent X-User-Id / X-User-Roles) ------------
Route::get('stages', [StageController::class, 'index']);
Route::post('stages', [StageController::class, 'store']);
Route::get('stages/{stage}', [StageController::class, 'show']);
Route::post('stages/{stage}/approve', [StageController::class, 'approve']);
Route::post('stages/{stage}/suspend', [StageController::class, 'suspend']);

Route::get('riders', [RiderController::class, 'index']);
Route::post('riders', [RiderController::class, 'store']);
Route::get('riders/{rider}', [RiderController::class, 'show']);
Route::post('riders/{rider}/suspend', [RiderController::class, 'suspend']);
Route::post('riders/{rider}/reinstate', [RiderController::class, 'reinstate']);
Route::post('riders/{rider}/photo', [PhotoController::class, 'rider']);

Route::post('bikes/{bike}/photo', [PhotoController::class, 'bike']);
Route::post('bikes/{bike}/stolen', [BikeController::class, 'flagStolen']);
Route::delete('bikes/{bike}/stolen', [BikeController::class, 'clearStolen']);

Route::get('incidents', [IncidentController::class, 'index']);
Route::post('incidents', [IncidentController::class, 'store']);
