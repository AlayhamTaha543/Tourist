<?php
use App\Http\Controllers\Api\Taxi\RatingController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('ratings')->group(function () {
    // Create a new generic rating
    Route::post('/', [RatingController::class, 'store'])->name('ratings.store');

    // Delete a rating
    Route::delete('{id}', [RatingController::class, 'destroy'])->name('ratings.destroy');
});