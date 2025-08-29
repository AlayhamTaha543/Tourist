<?php

use App\Http\Controllers\Api\Travel\TravelController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('travel')->controller(TravelController::class)->group(function () {
        Route::get('showAll', 'getAllFlights');
        Route::get('show/{id}', 'getFlight');
        Route::get('showAvailable', 'getAvailableFlights');
        Route::post('showAvailableDate', 'getAvailableFlightsDate');
        Route::get('showAgency/{id}', 'getAgency');
        Route::get('showAllAgency', 'getAllAgency');
        Route::post('bookFlight/{id}', 'bookFlight');
        Route::post('bookFlightByPoint/{id}', 'bookFlightByPoint');
        Route::post('updateFlightBooking/{id}', 'updateFlightBooking');
        Route::get('bookings', 'getNearestBookedFlight');
        Route::get('search', 'searchFlights');
    });
});
