<?php

namespace App\Services\interfaces;

use App\Models\DriverVehicleAssignment;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;

interface DriverVehicleAssignmentServiceInterface
{
    /**
     * Find active assignment for driver and vehicle
     *
     * @param int $driverId
     * @param int $vehicleId
     * @return \App\Models\DriverVehicleAssignment|null
     */
    public function findActive(int $driverId, int $vehicleId): ?DriverVehicleAssignment;

    /**
     * Assign a vehicle to a driver
     *
     * @param int $driverId
     * @param int $vehicleId
     * @return \App\Models\DriverVehicleAssignment
     * @throws \Exception
     */
    public function assign(int $driverId, int $vehicleId): DriverVehicleAssignment;

    /**
     * Unassign a vehicle from a driver
     *
     * @param int $assignmentId
     * @return void
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function unassign(int $assignmentId): void;

    /**
     * List active assignments by driver
     *
     * @param int $driverId
     * @return \Illuminate\Support\Collection
     */
    public function listActiveByDriver(int $driverId): Collection;

    /**
     * List active assignments by vehicle
     *
     * @param int $vehicleId
     * @return \Illuminate\Support\Collection
     */
    public function listActiveByVehicle(int $vehicleId): Collection;

    /**
     * Get assignment history with filters
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function history(array $filters = [], int $perPage = 15): Paginator;

    /**
     * Check if driver is available for assignment
     *
     * @param int $driverId
     * @return bool
     */
    public function checkDriverAvailable(int $driverId): bool;

    /**
     * Check if vehicle is available for assignment
     *
     * @param int $vehicleId
     * @return bool
     */
    public function checkVehicleAvailable(int $vehicleId): bool;

    /**
     * End all active assignments for a driver
     *
     * @param int $driverId
     * @return int
     */
    public function endAllForDriver(int $driverId): int;

    /**
     * End all active assignments for a vehicle
     *
     * @param int $vehicleId
     * @return int
     */
    public function endAllForVehicle(int $vehicleId): int;

    /**
     * Get assignment by ID
     *
     * @param int $assignmentId
     * @return \App\Models\DriverVehicleAssignment|null
     */
    public function getById(int $assignmentId): ?DriverVehicleAssignment;

    /**
     * Get active drivers for a vehicle
     *
     * @param int $vehicleId
     * @return \Illuminate\Support\Collection
     */
    public function driversForVehicle(int $vehicleId): Collection;

    /**
     * Get active vehicles for a driver
     *
     * @param int $driverId
     * @return \Illuminate\Support\Collection
     */
    public function vehiclesForDriver(int $driverId): Collection;
}