<?php

namespace App\Services\Rental;

use App\Enum\RentalVehicleStatus;
use App\Repositories\Interfaces\Rent\RentalVehicleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class RentalVehicleService
{
    public function __construct(
        protected RentalVehicleRepositoryInterface $vehicleRepository
    ) {}

    public function getAllVehicles(): Collection
    {
        return $this->vehicleRepository->all();
    }

    public function getPaginatedVehicles(int $perPage = 15): LengthAwarePaginator
    {
        return $this->vehicleRepository->paginate($perPage);
    }

    public function getVehicleById(int $id): ?object
    {
        return $this->vehicleRepository->find($id);
    }

    public function createVehicle(array $data): object
    {
        return $this->vehicleRepository->create($data);
    }

    public function updateVehicle(int $id, array $data): bool
    {
        return $this->vehicleRepository->update($id, $data);
    }

    public function deleteVehicle(int $id): bool
    {
        return $this->vehicleRepository->delete($id);
    }

    public function getVehiclesByOffice(int $officeId): Collection
    {
        return $this->vehicleRepository->findByOffice($officeId);
    }

    public function getVehiclesByStatus(RentalVehicleStatus $status): Collection
    {
        return $this->vehicleRepository->findByStatus($status);
    }

    public function updateVehicleStatus(int $vehicleId, RentalVehicleStatus $status): bool
    {
        return $this->vehicleRepository->updateStatus($vehicleId, $status);
    }

    public function getVehicleStatusHistory(int $vehicleId): Collection
    {
        return $this->vehicleRepository->getStatusHistory($vehicleId);
    }

    public function getVehicleByLicensePlate(string $licensePlate): ?object
    {
        return $this->vehicleRepository->findByLicensePlate($licensePlate);
    }

    public function changeVehicleOffice(int $vehicleId, int $newOfficeId): bool
    {
        return $this->vehicleRepository->update($vehicleId, ['office_id' => $newOfficeId]);
    }
}
