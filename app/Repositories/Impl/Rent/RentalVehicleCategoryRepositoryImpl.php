<?php

namespace App\Repositories\Impl\Rent;

use App\Models\RentalVehicleCategory;
use App\Repositories\Interfaces\Rent\RentalVehicleCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RentalVehicleCategoryRepositoryImpl implements RentalVehicleCategoryRepositoryInterface
{
    public function all(): Collection
    {
        return RentalVehicleCategory::all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return RentalVehicleCategory::paginate($perPage);
    }

    public function find(int $id): ?RentalVehicleCategory
    {
        return RentalVehicleCategory::find($id);
    }

    public function create(array $data): RentalVehicleCategory
    {
        return RentalVehicleCategory::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $category = RentalVehicleCategory::find($id);
        return $category ? $category->update($data) : false;
    }

    public function delete(int $id): bool
    {
        return RentalVehicleCategory::destroy($id) > 0;
    }

    public function findByName(string $name): ?RentalVehicleCategory
    {
        return RentalVehicleCategory::where('name', $name)->first();
    }

    public function vehicles(int $categoryId): Collection
    {
        return RentalVehicleCategory::find($categoryId)?->vehicles ?? collect();
    }
}
