<?php

use App\Http\Controllers\Api\Journey\JourneyController;
use App\Http\Controllers\Api\TourAdminRequestController;
use Illuminate\Support\Facades\Route;

Route::prefix('journey')->group(function () {
    Route::get('/flights/landingPage/countries', [JourneyController::class, 'getFlightsByCountries']);
    // Route::get('/flights/country/{country}', [JourneyController::class, 'getFlightsForCountry']);
    Route::get('/destinations', [JourneyController::class, 'getDestinations']);
    Route::get('/flights/search', [JourneyController::class, 'searchFlights']);
});

Route::middleware('auth:sanctum')->prefix('journey')->group(function () {
    Route::get('/flights/countries', [JourneyController::class, 'getFlightsByCountries']);
    Route::get('/flights/country/{country}', [JourneyController::class, 'getFlightsForCountry']);
    Route::get('/country/{country}', [JourneyController::class, 'getCountryDetails']);

});


Route::post('auth/requestTourAdmin', [TourAdminRequestController::class, 'store']);

require __DIR__ . '/api/auth.php';
require __DIR__ . '/api/booking.php';
require __DIR__ . '/api/favourite.php';
require __DIR__ . '/api/hotel.php';
require __DIR__ . '/api/location.php';
require __DIR__ . '/api/restaurants.php';
require __DIR__ . '/api/tour.php';
require __DIR__ . '/api/travel.php';
require __DIR__ . '/api/driver.php';
require __DIR__ . '/api/rating.php';
require __DIR__ . '/api/taxi.php';
require __DIR__ . '/api/rental.php';