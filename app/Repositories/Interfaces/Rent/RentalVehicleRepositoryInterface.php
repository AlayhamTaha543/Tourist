<?php

namespace App\Repositories\Interfaces\Rent;

use App\Enum\RentalVehicleStatus;
use App\Models\RentalVehicle;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface RentalVehicleRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): ?RentalVehicle;
    public function create(array $data): RentalVehicle;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findByLicensePlate(string $licensePlate): ?RentalVehicle;
    public function findByStatus(RentalVehicleStatus $status): Collection;
    public function findByOffice(int $officeId): Collection;
    public function updateStatus(int $vehicleId, RentalVehicleStatus $status): bool;
    public function getStatusHistory(int $vehicleId): Collection;
}
