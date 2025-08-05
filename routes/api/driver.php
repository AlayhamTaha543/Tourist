<?php

use App\Http\Controllers\Api\Taxi\DriverController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('drivers')->group(function () {
    Route::get('nearby', [DriverController::class, 'getNearbyDrivers'])->name('drivers.nearby');
    Route::get('available', [DriverController::class, 'available'])->name('drivers.available');
    Route::get('{id}/stats', [DriverController::class, 'stats'])->name('drivers.stats');
    Route::patch('{id}/location', [DriverController::class, 'updateLocation'])->name('drivers.updateLocation');
    Route::patch('{id}/availability', [DriverController::class, 'updateAvailability'])->name('drivers.updateAvailability');
});
