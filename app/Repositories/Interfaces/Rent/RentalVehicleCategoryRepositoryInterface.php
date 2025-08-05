<?php

namespace App\Repositories\Interfaces\Rent;

use App\Models\RentalVehicleCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface RentalVehicleCategoryRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): ?RentalVehicleCategory;
    public function create(array $data): RentalVehicleCategory;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findByName(string $name): ?RentalVehicleCategory;
    public function vehicles(int $categoryId): Collection;
}
