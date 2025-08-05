<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\Api\Tour\TourController;
use App\Http\Controllers\Api\Hotel\HotelController;
use App\Http\Controllers\Api\Travel\TravelController;
use App\Http\Controllers\Api\Restaurant\RestaurantController;

// ========== Public Auth Routes ==========
Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('signup', 'signup');
});

// ========== Protected Routes ==========
Route::middleware('auth:sanctum')->group(function () {

    // --- Auth Operations ---
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::get('resendOTPCode', 'resendOTPCode');
        Route::get('userInfo', 'userInfo');
        Route::post('OTPCode', 'OTPCode');
        Route::post('logout', 'logout');
    });

    // --- User Services ---
    Route::prefix('auth')->controller(ServiceController::class)->group(function () {
        Route::get('userRank', 'userRank');
        Route::get('discountPoints', 'discountPoints');
        Route::post('addRating', 'addRating');
        Route::post('submitFeedback', 'submitFeedback');
        Route::get('getAvailablePromotions', 'getAvailablePromotions');
        Route::post('requestTourAdmin', 'requestTourAdmin');
    });

    // --- Booking ---
    Route::prefix('booking')->controller(BookingController::class)->group(function () {
        Route::post('payForBooking/{id}', 'payForBooking');
        Route::get('getAllBookings', 'getAllBookings');
        Route::get('getBookingHistory', 'getBookingHistory');
        Route::get('cancelBooking/{id}', 'cancelBooking');
        Route::post('modifyBooking/{id}', 'modifyBooking');
    });

    // --- Locations ---
    Route::prefix('location')->controller(LocationController::class)->group(function () {
        Route::get('show/{id}', 'showLocation');
        Route::get('showAll', 'showAllLocation');
        Route::get('showAllLocationFilter', 'showAllLocationFilter');
    });

    // --- Travel ---
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
    });

    // --- Favourites ---
    Route::prefix('favourite')->controller(FavouriteController::class)->group(function () {
        Route::get('show/{id}', 'showFavourite');
        Route::get('showAll', 'showAllFavourite');
        Route::post('restaurant/{id}', 'addRestaurantToFavourite');
        Route::post('hotel/{id}', 'addHotelToFavourite');
        Route::post('tour/{id}', 'addTourToFavourite');
        Route::post('package/{id}', 'addPackageToFavourite');
        Route::post('delete/{id}', 'removeFromFavouriteById');
    });

    // --- Tours ---
    Route::prefix('tour')->controller(TourController::class)->group(function () {
        Route::get('show/{id}', 'showTour');
        Route::get('showAll', 'showAllTour');
        Route::post('bookTour/{id}', 'bookTour');
        Route::post('bookTourByPoint/{id}', 'bookTourByPoint');
    });

    // --- Hotels ---
    Route::prefix('hotel')->controller(HotelController::class)->group(function () {
        Route::get('show/{id}', 'showHotel');
        Route::get('showAll', 'showAllHotel');
        Route::get('showNearBy', 'showNearByHotel');
        Route::get('showAvailableRoom/{id}', 'showAvailableRoom');
        Route::post('showAvailableRoomType/{id}', 'showAvailableRoomType');
        Route::post('bookHotel/{id}', 'bookHotel');
    });

    // --- Restaurants ---
    Route::prefix('restaurants')->controller(RestaurantController::class)->group(function () {
        Route::get('show/{id}', 'showRestaurant');
        Route::get('showAll', 'showAllRestaurant');
        Route::get('showNearBy', 'showNearByRestaurant');
        Route::get('showRestaurantByLocation', 'showRestaurantByLocation');
        Route::get('showMenuItem/{id}', 'showMenuItem');
        Route::get('showMenuCategory/{id}', 'showMenuCategory');
        Route::get('showAvailableTable/{id}', 'showAvailableTable');
        Route::post('bookTable/{id}', 'bookTable');
    });
});

// ========== External Route Files ==========
require __DIR__ . '/api/driver.php';
require __DIR__ . '/api/rating.php';
require __DIR__ . '/api/taxi.php';
require __DIR__ . '/api/rental.php';