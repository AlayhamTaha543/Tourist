<?php

namespace App\Services\interfaces;

use App\Models\Driver;
use App\Models\Rating;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

interface DriverServiceInterface
{
    /**
     * Get all drivers
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllDrivers(): Collection;

    /**
     * Get a driver by ID
     *
     * @param int $id
     * @return \App\Models\Driver
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getDriverById(int $id): Driver;

    /**
     * Get drivers by taxi service
     *
     * @param int $taxiServiceId
     * @return array
     */
    public function getDriversByTaxiService(int $taxiServiceId): array;

    /**
     * Get available drivers
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableDrivers(): Collection;

    /**
     * Create a new driver
     *
     * @param array $data
     * @return \App\Models\Driver
     * @throws \Exception
     */
    public function createDriver(array $data): Driver;

    /**
     * Update a driver
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Driver
     * @throws \Exception
     */
    public function updateDriver(int $id, array $data): Driver;

    /**
     * Create driver rating
     *
     * @param int $userId
     * @param int $driverId
     * @param int $bookingId
     * @param float $ratingValue
     * @param string|null $comment
     * @return \App\Models\Rating
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function createDriverRating(
        int $userId,
        int $driverId,
        int $bookingId,
        float $ratingValue,
        ?string $comment = null
    ): Rating;

    /**
     * Delete a driver
     *
     * @param int $id
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function deleteDriver(int $id): bool;

    /**
     * Update driver location
     *
     * @param int $driverId
     * @param float $lat
     * @param float $lng
     * @return bool
     * @throws \Exception
     */
    public function updateDriverLocation(int $driverId, float $lat, float $lng): bool;

    /**
     * Mark driver as available
     *
     * @param int $driverId
     * @return bool
     * @throws \Exception
     */
    public function markAvailable(int $driverId): bool;

    /**
     * Mark driver as busy
     *
     * @param int $driverId
     * @return bool
     * @throws \Exception
     */
    public function markBusy(int $driverId): bool;

    /**
     * Mark driver as offline
     *
     * @param int $driverId
     * @return bool
     * @throws \Exception
     */
    public function markOffline(int $driverId): bool;

    /**
     * Get available drivers for booking
     *
     * @param int $taxiServiceId
     * @param string $bookingDateTime
     * @param float $pickupLat
     * @param float $pickupLng
     * @param int $radius
     * @param int|null $vehicleTypeId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableDriversForBooking(
        int $taxiServiceId,
        string $bookingDateTime,
        float $pickupLat,
        float $pickupLng,
        int $radius,
        ?int $vehicleTypeId = null
    ): Collection;

    /**
     * Get bookable nearby drivers
     *
     * @param string $bookingDateTime
     * @param float $pickupLat
     * @param float $pickupLng
     * @param float $radius
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBookableNearbyDrivers(
        string $bookingDateTime,
        float $pickupLat,
        float $pickupLng,
        float $radius
    ): Collection;

    /**
     * Get driver statistics
     *
     * @param int $driverId
     * @return array
     */
    public function getDriverStats(int $driverId): array;
}
