<?php

use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('booking')->controller(BookingController::class)->group(function () {
        Route::post('payForBooking/{id}', 'payForBooking');
        Route::get('getAllBookings', 'getAllBookings');
        Route::get('getBookingHistory', 'getBookingHistory');
        Route::get('cancelBooking/{id}', 'cancelBooking');
        Route::post('modifyBooking/{id}', 'modifyBooking');
    });
});