<?php

use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('location')->controller(LocationController::class)->group(function () {
        Route::get('show/{id}', 'showLocation');
        Route::get('showAll', 'showAllLocation');
        Route::get('showAllLocationFilter', 'showAllLocationFilter');
    });
});
