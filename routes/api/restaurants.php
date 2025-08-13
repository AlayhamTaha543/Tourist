<?php

use App\Http\Controllers\Api\Restaurant\RestaurantController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('restaurants')->controller(RestaurantController::class)->group(function () {
        Route::get('show/{id}', 'showRestaurant');
        Route::get('showAll', 'showAllRestaurant');
        Route::get('showAllNextTrip', 'showNextTripRestaurant');
        Route::get('showNearBy', 'showNearByRestaurant');
        Route::get('showRestaurantByLocation', 'showRestaurantByLocation');
        Route::get('showMenuItem/{id}', 'showMenuItem');
        Route::get('showMenuCategory/{id}', 'showMenuCategory');
        Route::get('showAvailableTable/{id}', 'showAvailableTable');
        Route::post('bookTable/{id}', 'bookTable');
    });
});
