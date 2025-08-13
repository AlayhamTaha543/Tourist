<?php

use App\Http\Controllers\Api\Hotel\HotelController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('hotel')->controller(HotelController::class)->group(function () {
        Route::get('show/{id}', 'showHotel');
        Route::get('showAll', 'showAllHotel');
        Route::get('showAllNextTrip', 'showNextTripHotel');
        Route::get('showNearBy', 'showNearByHotel');
        Route::get('showAvailableRoom/{id}', 'showAvailableRoom');
        Route::post('showAvailableRoomType/{id}', 'showAvailableRoomType');
        Route::post('bookHotel/{id}', 'bookHotel');
    });
});
