<?php

namespace App\Repositories\Impl;

use App\Models\DriverVehicleAssignment;
use App\Repositories\Interfaces\Taxi\DriverVehicleAssignmentRepositoryInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DriverVehicleAssignmentRepository implements DriverVehicleAssignmentRepositoryInterface
{
    public function __construct(
        protected DriverVehicleAssignment $model
    ) {
    }

    public function findActiveAssignment(int $driverId, int $vehicleId): ?object
    {
        return $this->model->where([
            'driver_id' => $driverId,
            'vehicle_id' => $vehicleId,
        ])
            ->whereNull('unassigned_at')
            ->first();
    }

    public function assignDriver(array $data): object
    {
        return DB::transaction(function () use ($data) {
            // Prevent duplicate active assignments
            $this->endActiveAssignments($data['driver_id'], $data['vehicle_id']);

            return $this->model->create([
                'driver_id' => $data['driver_id'],
                'vehicle_id' => $data['vehicle_id'],
                'assigned_at' => now(),
            ]);
        });
    }

    public function unassignDriver(int $assignmentId): bool
    {
        $assignment = $this->model->findOrFail($assignmentId);

        if ($assignment->unassigned_at) {
            throw new \LogicException('Assignment already unassigned');
        }

        $assignment->update(['unassigned_at' => now()]);
        return true;
    }

    public function getActiveAssignmentsByDriver(int $driverId): Collection
    {
        return $this->model->with(['vehicle', 'driver'])
            ->where('driver_id', $driverId)
            ->whereNull('unassigned_at')
            ->get();
    }

    public function getActiveAssignmentsByVehicle(int $vehicleId): Collection
    {
        return $this->model->with(['driver', 'vehicle'])
            ->where('vehicle_id', $vehicleId)
            ->whereNull('unassigned_at')
            ->get();
    }

    public function getAssignmentHistory(array $filters = [], int $perPage = 15): Paginator
    {
        return $this->model->with(['driver', 'vehicle'])
            ->when(isset($filters['driver_id']), fn($q) => $q->where('driver_id', $filters['driver_id']))
            ->when(isset($filters['vehicle_id']), fn($q) => $q->where('vehicle_id', $filters['vehicle_id']))
            ->when(isset($filters['date']), fn($q) => $q->whereDate('assigned_at', $filters['date']))
            ->orderBy('assigned_at', 'desc')
            ->paginate($perPage);
    }

    public function isDriverAvailable(int $driverId): bool
    {
        return !$this->model->where('driver_id', $driverId)
            ->whereNull('unassigned_at')
            ->exists();
    }

    public function isVehicleAvailable(int $vehicleId): bool
    {
        return !$this->model->where('vehicle_id', $vehicleId)
            ->whereNull('unassigned_at')
            ->exists();
    }

    public function endAllActiveAssignmentsForDriver(int $driverId): bool
    {
        return $this->model->where('driver_id', $driverId)
            ->whereNull('unassigned_at')
            ->update(['unassigned_at' => now()]) > 0;
    }

    public function endAllActiveAssignmentsForVehicle(int $vehicleId): bool
    {
        return $this->model->where('vehicle_id', $vehicleId)
            ->whereNull('unassigned_at')
            ->update(['unassigned_at' => now()]) > 0;
    }

    public function findAssignmentById(int $assignmentId): ?object
    {
        return $this->model->with(['driver', 'vehicle'])->find($assignmentId);
    }

    public function getActiveDriversForVehicle(int $vehicleId): Collection
    {
        return $this->model->with('driver')
            ->where('vehicle_id', $vehicleId)
            ->whereNull('unassigned_at')
            ->get()
            ->pluck('driver');
    }

    public function getActiveVehiclesForDriver(int $driverId): Collection
    {
        return $this->model->with('vehicle')
            ->where('driver_id', $driverId)
            ->whereNull('unassigned_at')
            ->get()
            ->pluck('vehicle');
    }

    protected function endActiveAssignments(int $driverId, int $vehicleId): void
    {
        // End any existing active assignments for this driver
        $this->endAllActiveAssignmentsForDriver($driverId);

        // End any existing active assignments for this vehicle
        $this->endAllActiveAssignmentsForVehicle($vehicleId);
    }
}
