<?php
use App\Http\Controllers\Api\Taxi\DriverController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Taxi\TaxiBookingController;
use App\Http\Controllers\Api\Taxi\TaxiServiceController;
use App\Http\Controllers\Api\Taxi\TripController;
use App\Http\Controllers\Api\Taxi\VehicleController;

Route::middleware('auth:sanctum')->prefix('trips')->group(function () {
    // List all trips
    Route::get('/', [TripController::class, 'index'])->name('trips.index');

    // Create a new trip
    Route::post('/', [TripController::class, 'store'])->name('trips.store');

    // Show a specific trip (ID in body)
    Route::post('/show', [TripController::class, 'show'])->name('trips.show');

    // Update/complete a trip (ID in body)
    Route::post('/complete', [TripController::class, 'complete'])->name('trips.complete');

    // Cancel a trip (ID in body)
    Route::post('/cancel', [TripController::class, 'cancel'])->name('trips.cancel');

    // Accept a trip (ID in body)
    Route::post('/accept', [TripController::class, 'accept'])->name('trips.accept');

    // Start a trip (ID in body)
    Route::post('/start', [TripController::class, 'start'])->name('trips.start');

    // Rate a trip (ID in body)
    Route::post('/rate', [TripController::class, 'rate'])->name('trips.rate');

    // Delete a trip (ID in body)
    Route::post('/delete', [TripController::class, 'destroy'])->name('trips.destroy');

    // Get nearby trips (search/filter)
    Route::get('/nearby', [TripController::class, 'nearby'])->name('trips.nearby');
});

Route::middleware('auth:sanctum')->prefix('taxi-services')->group(function () {
    // List all taxi services (paginated)
    Route::get('/', [TaxiServiceController::class, 'index'])->name('taxi-services.index');

    // Create a new taxi service
    Route::post('/', [TaxiServiceController::class, 'store'])->name('taxi-services.store');

    // Show a specific taxi service (ID in body)
    Route::post('/show', [TaxiServiceController::class, 'show'])->name('taxi-services.show');

    // Update a taxi service (ID in body)
    Route::post('/update', [TaxiServiceController::class, 'update'])->name('taxi-services.update');

    // Delete a taxi service (ID in body)
    Route::post('/delete', [TaxiServiceController::class, 'destroy'])->name('taxi-services.destroy');

    // Get taxi services by location (locationId in body)
    Route::get('/by-location', [TaxiServiceController::class, 'getByLocation'])->name('taxi-services.by-location');
});

Route::middleware('auth:sanctum')->prefix('taxi-bookings')->group(function () {
    // List all bookings for the authenticated user
    Route::get('/', [TaxiBookingController::class, 'index'])->name('api.taxi-bookings.index');

    // Create a new booking
    Route::post('/', [TaxiBookingController::class, 'store'])->name('api.taxi-bookings.store');

    // Show a specific booking (ID in body)
    Route::get('/details', [TaxiBookingController::class, 'show'])->name('api.taxi-bookings.show');

    // Update a booking (ID in body)
    Route::post('/update', [TaxiBookingController::class, 'update'])->name('api.taxi-bookings.update');

    // Cancel a booking (ID in body)
    Route::post('/cancel', [TaxiBookingController::class, 'cancel'])->name('api.taxi-bookings.cancel');

    // Get nearby drivers for a booking (parameters in body)
    Route::post('/nearby-drivers', [TaxiBookingController::class, 'nearbyDrivers'])->name('api.taxi-bookings.nearby-drivers');

    // Get available vehicles for a booking (parameters in body)
    Route::post('/available-vehicles', [TaxiBookingController::class, 'availableVehicles'])->name('api.taxi-bookings.available-vehicles');
});

Route::middleware('auth:sanctum')->prefix('vehicles')->group(function () {
    // Public routes (require authentication but no specific permission)
    Route::get('/', [VehicleController::class, 'index'])->name('api.vehicles.index');
    Route::get('/available', [VehicleController::class, 'available'])->name('api.vehicles.available');

    // Get vehicles by taxi service (taxiServiceId in body)
    Route::post('/by-taxi-service', [VehicleController::class, 'getByTaxiService'])->name('api.vehicles.by-taxi-service');

    // Show specific vehicle (ID in body)
    Route::post('/show', [VehicleController::class, 'show'])->name('api.vehicles.show');
});

Route::middleware('auth:sanctum')->prefix('drivers')->group(function () {
    // List all drivers
    Route::get('/', [DriverController::class, 'index'])->name('api.drivers.index');

    // Create a new driver
    Route::post('/', [DriverController::class, 'store'])->name('api.drivers.store');

    // Show a specific driver (ID in body)
    Route::post('/show', [DriverController::class, 'show'])->name('api.drivers.show');

    // Update a driver (ID in body)
    Route::post('/update', [DriverController::class, 'update'])->name('api.drivers.update');

    // Delete a driver (ID in body)
    Route::post('/delete', [DriverController::class, 'destroy'])->name('api.drivers.destroy');

    // Get drivers by taxi service (taxiServiceId in body)
    Route::post('/by-taxi-service', [DriverController::class, 'getByTaxiService'])->name('api.drivers.by-taxi-service');

    // Get available drivers
    Route::get('/available', [DriverController::class, 'getAvailableDrivers'])->name('api.drivers.available');

    // Update driver rating (ID in body)
    Route::post('/rate', [DriverController::class, 'updateRating'])->name('api.drivers.rate');

    // Update driver location (ID in body)
    Route::post('/location', [DriverController::class, 'updateLocation'])->name('api.drivers.location.update');

    // Update driver availability status (ID in body)
    Route::post('/availability', [DriverController::class, 'updateAvailabilityStatus'])->name('api.drivers.availability.update');
});