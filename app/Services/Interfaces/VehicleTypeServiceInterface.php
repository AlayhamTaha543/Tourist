<?php

namespace App\Services\interfaces;

use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Collection;

interface VehicleTypeServiceInterface
{
    /**
     * Get all vehicle types
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllVehicleTypes(): Collection;

    /**
     * Get a vehicle type by ID
     *
     * @param int $id
     * @return \App\Models\VehicleType
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getVehicleTypeById(int $id): VehicleType;

    /**
     * Get vehicle types by taxi service
     *
     * @param int $taxiServiceId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVehicleTypesByTaxiService(int $taxiServiceId): Collection;

    /**
     * Create a new vehicle type
     *
     * @param array $data
     * @return \App\Models\VehicleType
     * @throws \Exception
     */
    public function createVehicleType(array $data): VehicleType;

    /**
     * Update a vehicle type
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\VehicleType
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function updateVehicleType(int $id, array $data): VehicleType;

    /**
     * Delete a vehicle type
     *
     * @param int $id
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function deleteVehicleType(int $id): bool;

    /**
     * Toggle active status of a vehicle type
     *
     * @param int $id
     * @return \App\Models\VehicleType
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function toggleVehicleTypeStatus(int $id): VehicleType;
}
