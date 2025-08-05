<?php

namespace App\Services\Rental;

use App\Repositories\Interfaces\Rent\RentalOfficeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RentalOfficeService
{
    public function __construct(
        protected RentalOfficeRepositoryInterface $officeRepository
    ) {}

    public function getAllOffices(): Collection
    {
        return $this->officeRepository->all();
    }

    public function getPaginatedOffices(int $perPage = 15): LengthAwarePaginator
    {
        return $this->officeRepository->paginate($perPage);
    }

    public function getOfficeById(int $id, bool $withRelations = false): ?object
    {
        if ($withRelations) {
            return $this->officeRepository->withRelations($id, ['location', 'manager', 'vehicles']);
        }
        return $this->officeRepository->find($id);
    }

    public function createOffice(array $data): object
    {
        return $this->officeRepository->create($data);
    }

    public function updateOffice(int $id, array $data): bool
    {
        return $this->officeRepository->update($id, $data);
    }

    public function deleteOffice(int $id): bool
    {
        return $this->officeRepository->delete($id);
    }

    public function getOfficesByLocation(int $locationId): Collection
    {
        return $this->officeRepository->findByLocation($locationId);
    }

    public function getOfficesByManager(int $managerId): Collection
    {
        return $this->officeRepository->findByManager($managerId);
    }
}
