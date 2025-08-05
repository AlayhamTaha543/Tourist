<?php
use App\Http\Controllers\Api\Taxi\RatingController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('ratings')->group(function () {
    // Create a new driver rating
    Route::post('/', [RatingController::class, 'store'])->name('ratings.store');

    // Delete a rating
    Route::delete('{id}', [RatingController::class, 'destroy'])->name('ratings.destroy');
});

// Public or protected (depending on your needs) routes for fetching ratings and averages
Route::prefix('drivers')->group(function () {
    // Get all ratings for a driver (paginated)
    Route::get('{driverId}/ratings', [RatingController::class, 'getDriverRatings'])->name('drivers.ratings');

    // Get average rating for a driver
    Route::get('{driverId}/average-rating', [RatingController::class, 'getDriverAverage'])->name('drivers.average-rating');
});
