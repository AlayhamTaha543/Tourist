<?php

namespace App\Repositories\Interfaces\Taxi;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;

interface DriverVehicleAssignmentRepositoryInterface
{
    public function findActiveAssignment(int $driverId, int $vehicleId): ?object;
    public function assignDriver(array $data): object;
    public function unassignDriver(int $assignmentId): bool;
    public function getActiveAssignmentsByDriver(int $driverId): Collection;
    public function getActiveAssignmentsByVehicle(int $vehicleId): Collection;
    public function getAssignmentHistory(array $filters = [], int $perPage = 15): Paginator;
    public function isDriverAvailable(int $driverId): bool;
    public function isVehicleAvailable(int $vehicleId): bool;
    public function endAllActiveAssignmentsForDriver(int $driverId): bool;
    public function endAllActiveAssignmentsForVehicle(int $vehicleId): bool;
    public function findAssignmentById(int $assignmentId): ?object;
    public function getActiveDriversForVehicle(int $vehicleId): Collection;
    public function getActiveVehiclesForDriver(int $driverId): Collection;
}
