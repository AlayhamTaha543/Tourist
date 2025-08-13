<?php

use App\Http\Controllers\Api\Tour\TourController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('tour')->controller(TourController::class)->group(function () {
        Route::get('show/{id}', 'showTour');
        Route::get('showAll', 'showAllTour');
        Route::post('bookTour/{id}', 'bookTour');
        Route::post('bookTourByPoint/{id}', 'bookTourByPoint');
    });
});
