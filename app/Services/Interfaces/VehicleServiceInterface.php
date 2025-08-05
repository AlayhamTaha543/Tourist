<?php

namespace App\Services\interfaces;

use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

interface VehicleServiceInterface
{
    /**
     * Get all vehicles
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllVehicles(): Collection;

    /**
     * Get a vehicle by ID
     *
     * @param int $id
     * @return \App\Models\Vehicle
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getVehicleById(int $id): Vehicle;

    /**
     * Get vehicles by taxi service
     *
     * @param int $taxiServiceId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVehiclesByTaxiService(int $taxiServiceId): Collection;

    /**
     * Get vehicles by vehicle type
     *
     * @param int $vehicleTypeId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVehiclesByType(int $vehicleTypeId): Collection;

    /**
     * Get available vehicles for booking
     *
     * @param int $taxiServiceId
     * @param int $vehicleTypeId
     * @param string $bookingDateTime
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableVehiclesForBooking(
        int $taxiServiceId,
        int $vehicleTypeId,
        string $bookingDateTime
    ): Collection;

    /**
     * Create a new vehicle
     *
     * @param array $data
     * @return \App\Models\Vehicle
     * @throws \Exception
     */
    public function createVehicle(array $data): Vehicle;

    /**
     * Update a vehicle
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Vehicle
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function updateVehicle(int $id, array $data): Vehicle;

    /**
     * Delete a vehicle
     *
     * @param int $id
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function deleteVehicle(int $id): bool;

    /**
     * Check if a vehicle is available at a specific time
     *
     * @param Vehicle|int $vehicle
     * @param Carbon|null $bookingTime
     * @return bool
     */
    public function isVehicleAvailable($vehicle, ?Carbon $bookingTime = null): bool;
}
