<?php

namespace App\Repositories\Impl;

use App\Models\VehicleType;
use Illuminate\Database\Eloquent\Collection;

class VehicleTypeRepository
{
    /**
     * Get all vehicle types
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all(): Collection
    {
        return VehicleType::with(['taxiService'])->get();
    }


    /**
     * Get a vehicle type by ID or fail
     *
     * @param int $id
     * @return \App\Models\VehicleType
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFail(int $id): VehicleType
    {
        return VehicleType::with(['taxiService'])->findOrFail($id);
    }

    /**
     * Get active vehicle types
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActive(): Collection
    {
        return VehicleType::where('is_active', true)
            ->with(['taxiService'])
            ->get();
    }

    /**
     * Get vehicle types by taxi service
     *
     * @param int $taxiServiceId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByTaxiService(int $taxiServiceId): Collection
    {
        return VehicleType::where('taxi_service_id', $taxiServiceId)
            ->where('is_active', true)
            ->get();
    }

    /**
     * Create a new vehicle type
     *
     * @param array $data
     * @return \App\Models\VehicleType
     */
    public function create(array $data): VehicleType
    {
        return VehicleType::create($data);
    }

    /**
     * Update a vehicle type
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        return VehicleType::where('id', $id)->update($data);
    }

    /**
     * Delete a vehicle type
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return VehicleType::where('id', $id)->delete();
    }
}