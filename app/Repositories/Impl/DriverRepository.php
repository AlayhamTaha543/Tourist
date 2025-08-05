<?php

namespace App\Repositories\Impl;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class DriverRepository
{
    /**
     * Get all drivers
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(): Collection
    {
        return Driver::with(['user', 'taxiService'])->get();
    }



    /**
     * Get a driver by ID or fail
     *
     * @param int $id
     * @return \App\Models\Driver
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id): Driver
    {
        return Driver::with(['user', 'taxiService'])->findOrFail($id);
    }

    /**
     * Get all available drivers
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allAvailable(): Collection
    {
        return Driver::where('availability_status', 'available')
            ->where('is_active', true)
            ->with(['user', 'taxiService'])
            ->get();
    }

    /**
     * Get drivers by taxi service
     *
     * @param int $taxiServiceId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByTaxiService(int $taxiServiceId): Collection
    {
        return Driver::where('taxi_service_id', $taxiServiceId)
            ->where('is_active', true)
            ->with(['user'])
            ->get();
    }
    public function updateAvailability(int $driverId, string $status): bool
    {
        if (!in_array($status, ['available', 'busy', 'offline'])) {
            throw new \InvalidArgumentException('Invalid status');
        }

        return $this->update($driverId, ['availability_status' => $status]);
    }

    /**
     * Create a new driver
     *
     * @param array $data
     * @return \App\Models\Driver
     */
    public function create(array $data): Driver
    {
        return Driver::create($data);
    }

    /**
     * Update a driver
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        return Driver::where('id', $id)->update($data);
    }

    /**
     * Update driver location
     *
     * @param int $driverId
     * @param float $lat
     * @param float $lng
     * @return bool
     */
    public function updateLocation(int $driverId, float $lat, float $lng): bool
    {
        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            throw new InvalidArgumentException("Invalid coordinates");
        }

        return Driver::where('id', $driverId)->update([
            'current_location' => DB::raw("ST_SRID(POINT(?, ?), 4326)"),
            [$lng, $lat]
        ]);
    }

    /**
     * Update driver rating
     *
     * @param int $id
     * @param float $rating
     * @return \App\Models\Driver
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function updateDriverRating(int $id, float $rating): Driver
{
    $driver = Driver::findOrFail($id);
    $driver->Rating = $rating;
    $driver->save();
    return $driver->fresh();
}
    /**
     * Delete a driver
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return Driver::where('id', $id)->delete();
    }

    /**
 * Fetch nearby available drivers from any taxi service within a radius.
 */
        public function getNearbyDriversFromAnyService(
            float $pickupLng,
            float $pickupLat,
            float $radiusKm
        ): Collection {
            return Driver::selectRaw(
                "*, ST_Distance_Sphere(current_location, POINT(?, ?)) as distance",
                [$pickupLng, $pickupLat]
            )
            ->where('availability_status', 'available')
            ->where('is_active', true)
            ->whereHas('activeVehicle', fn($q) => $q->where('is_active', true))
            ->whereRaw(
                "ST_Distance_Sphere(current_location, POINT(?, ?)) <= ?",
                [$pickupLng, $pickupLat, $radiusKm * 1000] // Convert km to meters
            )
            ->orderBy('distance')
            ->with(['user', 'taxiService', 'activeVehicle'])
            ->get();
        }
        /**
         * Fetch available drivers for a specific taxi service within a radius.
         */
        public function getNearbyDriversForTaxiService(
            int $taxiServiceId,
            float $pickupLng,
            float $pickupLat,
            float $maxDistanceKm = 5
        ): Collection {
            return Driver::where('taxi_service_id', $taxiServiceId)
                ->where('availability_status', 'available')
                ->where('is_active', true)
                ->whereHas('activeVehicle', fn($q) => $q->where('is_active', true))
                ->whereRaw(
                    "ST_Distance_Sphere(current_location, POINT(?, ?)) <= ?",
                    [$pickupLng, $pickupLat, $maxDistanceKm * 1000] // Convert km to meters
                )
                ->orderByRaw(
                    "ST_Distance_Sphere(current_location, POINT(?, ?)) ASC",
                    [$pickupLng, $pickupLat]
                )
                ->with(['activeVehicle.vehicleType'])
                ->get();
        }
}
