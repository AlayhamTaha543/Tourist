<?php

use App\Http\Controllers\Api\Rental\RentalBookingController;
use App\Http\Controllers\Api\Rental\RentalOfficeController;
use App\Http\Controllers\Api\Rental\RentalVehicleCategoryController;
use App\Http\Controllers\Api\Rental\RentalVehicleController;
use Illuminate\Support\Facades\Route;

Route::prefix('rental')->group(function () {
    // Rental Offices
    Route::prefix('/offices')->group(function () {
        Route::get('/', [RentalOfficeController::class, 'index']);
        Route::post('/', [RentalOfficeController::class, 'store']);
        Route::get('/{id}', [RentalOfficeController::class, 'show']);
        Route::put('/{id}', [RentalOfficeController::class, 'update']);
        Route::delete('/{id}', [RentalOfficeController::class, 'destroy']);
        Route::get('/location/{locationId}', [RentalOfficeController::class, 'getByLocation']);
    });

    // Vehicle Categories
    Route::prefix('/vehicle-categories')->group(function () {
        Route::get('/', [RentalVehicleCategoryController::class, 'index']);
        Route::post('/', [RentalVehicleCategoryController::class, 'store']);
        Route::get('/{id}', [RentalVehicleCategoryController::class, 'show']);
        Route::put('/{id}', [RentalVehicleCategoryController::class, 'update']);
        Route::delete('/{id}', [RentalVehicleCategoryController::class, 'destroy']);
        Route::get('/{id}/vehicles', [RentalVehicleCategoryController::class, 'getCategoryVehicles']);
    });

    // Vehicles
    Route::prefix('/vehicles')->group(function () {
        Route::get('/', [RentalVehicleController::class, 'index']);
        Route::post('/', [RentalVehicleController::class, 'store']);
        Route::get('/{id}', [RentalVehicleController::class, 'show']);
        Route::put('/{id}', [RentalVehicleController::class, 'update']);
        Route::delete('/{id}', [RentalVehicleController::class, 'destroy']);
        Route::put('/{id}/status', [RentalVehicleController::class, 'updateStatus']);
        Route::get('/{id}/status-history', [RentalVehicleController::class, 'getStatusHistory']);
        Route::get('/office/{officeId}', [RentalVehicleController::class, 'getByOffice']);
    });

    // Bookings
    Route::prefix('/bookings')->group(function () {
        Route::get('/', [RentalBookingController::class, 'index']);
        Route::post('/', [RentalBookingController::class, 'store']);
        Route::get('/{id}', [RentalBookingController::class, 'show']);
        Route::put('/{id}', [RentalBookingController::class, 'update']);
        Route::delete('/{id}', [RentalBookingController::class, 'destroy']);
        Route::post('/{id}/start', [RentalBookingController::class, 'startBooking']);
        Route::post('/{id}/complete', [RentalBookingController::class, 'completeBooking']);
        Route::get('/customer/{customerId}', [RentalBookingController::class, 'getByCustomer']);
        Route::get('/check-availability/{vehicleId}', [RentalBookingController::class, 'checkAvailability']);
    });
});
