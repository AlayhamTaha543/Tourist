<?php
namespace App\Services\Driver;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;
use App\Models\DriverVehicleAssignment;
use App\Repositories\Impl\DriverVehicleAssignmentRepository;
use Exception;

class DriverVehicleAssignmentService
{
    public function __construct(
        protected DriverVehicleAssignmentRepository $repo
    ) {
    }

    public function findActive(int $driverId, int $vehicleId): ?DriverVehicleAssignment
    {
        return $this->repo->findActiveAssignment($driverId, $vehicleId);
    }

    public function assign(int $driverId, int $vehicleId): DriverVehicleAssignment
    {
        // Step 1: Validate availability
        if (!$this->repo->isDriverAvailable($driverId)) {
            throw new Exception("Driver #{$driverId} is already assigned.");
        }
        if (!$this->repo->isVehicleAvailable($vehicleId)) {
            throw new Exception("Vehicle #{$vehicleId} is already in use.");
        }

        // Step 2: Delegate to repository (already transactional & ends old assignments)
        return $this->repo->assignDriver([
            'driver_id' => $driverId,
            'vehicle_id' => $vehicleId,
        ]);
    }

    public function unassign(int $assignmentId): void
    {
        $assignment = $this->repo->findAssignmentById($assignmentId)
            ?? throw new ModelNotFoundException("Assignment #{$assignmentId} not found.");

        if ($assignment->unassigned_at) {
            throw new Exception("Assignment #{$assignmentId} is already ended.");
        }

        $this->repo->unassignDriver($assignmentId);
    }

    public function listActiveByDriver(int $driverId): Collection
    {
        return $this->repo->getActiveAssignmentsByDriver($driverId);
    }

    public function listActiveByVehicle(int $vehicleId): Collection
    {
        return $this->repo->getActiveAssignmentsByVehicle($vehicleId);
    }

    public function history(array $filters = [], int $perPage = 15): Paginator
    {
        return $this->repo->getAssignmentHistory($filters, $perPage);
    }

    public function checkDriverAvailable(int $driverId): bool
    {
        return $this->repo->isDriverAvailable($driverId);
    }

    public function checkVehicleAvailable(int $vehicleId): bool
    {
        return $this->repo->isVehicleAvailable($vehicleId);
    }

    public function endAllForDriver(int $driverId): int
    {
        return $this->repo->endAllActiveAssignmentsForDriver($driverId);
    }

    public function endAllForVehicle(int $vehicleId): int
    {
        return $this->repo->endAllActiveAssignmentsForVehicle($vehicleId);
    }

    public function getById(int $assignmentId): ?DriverVehicleAssignment
    {
        return $this->repo->findAssignmentById($assignmentId);
    }

    public function driversForVehicle(int $vehicleId): Collection
    {
        return $this->repo->getActiveDriversForVehicle($vehicleId);
    }

    public function vehiclesForDriver(int $driverId): Collection
    {
        return $this->repo->getActiveVehiclesForDriver($driverId);
    }
}
