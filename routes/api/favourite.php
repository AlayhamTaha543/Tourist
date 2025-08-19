<?php

use App\Http\Controllers\FavouriteController;
use Illuminate\Support\Facades\Route;
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('favourite')->controller(FavouriteController::class)->group(function () {
        Route::get('show/{id}', 'showFavourite');
        Route::get('showAll', 'showAllFavourite');
        Route::post('restaurant/{id}', 'addRestaurantToFavourite');
        Route::post('hotel/{id}', 'addHotelToFavourite');
        Route::post('tour/{id}', 'addTourToFavourite');
        Route::post('package/{id}', 'addPackageToFavourite');
        Route::post('delete/{id}', 'removeFromFavouriteById');
        Route::post('country/{id}', 'addCountryToFavourite');
    });
});
