<?php

namespace App\Repositories\Impl\Rent;

use App\Models\RentalOffice;
use App\Repositories\Interfaces\Rent\RentalOfficeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RentalOfficeRepositoryImpl implements RentalOfficeRepositoryInterface
{
    public function all(): Collection
    {
        return RentalOffice::all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return RentalOffice::paginate($perPage);
    }

    public function find(int $id): ?RentalOffice
    {
        return RentalOffice::find($id);
    }

    public function create(array $data): RentalOffice
    {
        return RentalOffice::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $office = RentalOffice::find($id);
        return $office ? $office->update($data) : false;
    }

    public function delete(int $id): bool
    {
        return RentalOffice::destroy($id) > 0;
    }

    public function withRelations(int $id, array $relations): ?RentalOffice
    {
        return RentalOffice::with($relations)->find($id);
    }

    public function findByManager(int $managerId): Collection
    {
        return RentalOffice::where('manager_id', $managerId)->get();
    }

    public function findByLocation(int $locationId): Collection
    {
        return RentalOffice::where('location_id', $locationId)->get();
    }
}
