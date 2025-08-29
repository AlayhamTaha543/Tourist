<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('signup', 'signup');
    Route::post('OTPCode', 'OTPCode');
});
Route::middleware('auth:sanctum')->group(function () {

    // --- Auth Operations ---
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::get('resendOTPCode', 'resendOTPCode');
        Route::get('userInfo', 'userInfo');
        Route::post('/profile/edit', [AuthController::class, 'editProfile']);
        Route::post('logout', 'logout');
    });

    // --- User Services ---
    Route::prefix('auth')->controller(ServiceController::class)->group(function () {
        Route::get('userRank', 'userRank');
        Route::get('discountPoints', 'discountPoints');
        Route::post('addRating', 'addRating');
        Route::get('getAllFeedbacks', 'getAllFeedbacks');
        Route::get('getFeedbacksByType', 'getFeedbacksByType');
        Route::post('submitFeedback', 'submitFeedback');
        Route::get('getAvailablePromotions', 'getAvailablePromotions');
        // Route::post('requestTourAdmin', 'requestTourAdmin');
    });
});
