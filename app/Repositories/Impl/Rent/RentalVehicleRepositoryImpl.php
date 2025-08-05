<?php

namespace App\Repositories\Impl\Rent;

use App\Enum\RentalVehicleStatus;
use App\Models\RentalVehicle;
use App\Models\RentalVehicleStatusHistory;
use App\Repositories\Interfaces\Rent\RentalVehicleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class RentalVehicleRepositoryImpl implements RentalVehicleRepositoryInterface
{
    public function all(): Collection
    {
        return RentalVehicle::all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return RentalVehicle::with(['office', 'category'])->paginate($perPage);
    }

    public function find(int $id): ?RentalVehicle
    {
        return RentalVehicle::find($id);
    }

    public function create(array $data): RentalVehicle
    {
        return RentalVehicle::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $vehicle = RentalVehicle::find($id);
        return $vehicle ? $vehicle->update($data) : false;
    }

    public function delete(int $id): bool
    {
        return RentalVehicle::destroy($id) > 0;
    }

    public function findByLicensePlate(string $licensePlate): ?RentalVehicle
    {
        return RentalVehicle::where('license_plate', $licensePlate)->first();
    }

    public function findByStatus(RentalVehicleStatus $status): Collection
    {
        return RentalVehicle::where('status', $status)->get();
    }

    public function findByOffice(int $officeId): Collection
    {
        return RentalVehicle::where('office_id', $officeId)->get();
    }

    public function updateStatus(int $vehicleId, RentalVehicleStatus $status): bool
    {
        return DB::transaction(function () use ($vehicleId, $status) {
            $vehicle = RentalVehicle::find($vehicleId);

            if (!$vehicle) {
                return false;
            }

            // Record status change history
            RentalVehicleStatusHistory::create([
                'vehicle_id' => $vehicleId,
                'old_status' => $vehicle->status,
                'new_status' => $status,
                'changed_by_id' => auth()->id ?? 1, // Fallback to admin ID 1   ///
            ]);

            // Update vehicle status
            $vehicle->status = $status;
            return $vehicle->save();
        });
    }

    public function getStatusHistory(int $vehicleId): Collection
    {
        return RentalVehicleStatusHistory::where('vehicle_id', $vehicleId)
            ->orderBy('changed_at', 'desc')
            ->get();
    }
}
