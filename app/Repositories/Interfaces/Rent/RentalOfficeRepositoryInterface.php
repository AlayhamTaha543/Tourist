<?php

namespace App\Repositories\Interfaces\Rent;

use App\Models\RentalOffice;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface RentalOfficeRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): ?RentalOffice;
    public function create(array $data): RentalOffice;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function withRelations(int $id, array $relations): ?RentalOffice;
    public function findByManager(int $managerId): Collection;
    public function findByLocation(int $locationId): Collection;
}
