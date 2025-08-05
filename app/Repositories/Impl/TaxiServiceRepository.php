<?php
namespace App\Repositories\Impl;

use App\Models\TaxiService;
use App\Repositories\Interfaces\Taxi\TaxiServiceRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class TaxiServiceRepository implements TaxiServiceRepositoryInterface
{
    protected function baseQuery(bool $withRelations = false, bool $activeOnly = false): Builder
    {
        $query = TaxiService::query();

        if ($withRelations) {
            $query->with(['location', 'manager', 'vehicleTypes']);
        }

        if ($activeOnly) {
            $query->active();
        }

        return $query;
    }

    public function all(bool $withRelations = false, bool $activeOnly = false): Collection
    {
        return $this->baseQuery($withRelations, $activeOnly)->get();
    }

    public function find(int $id, bool $withRelations = false, bool $activeOnly = false): ?TaxiService
    {
        return $this->baseQuery($withRelations, $activeOnly)->find($id);
    }

    public function findOrFail(int $id, bool $withRelations = false, bool $activeOnly = false): TaxiService
    {
        $result = $this->find($id, $withRelations, $activeOnly);

        if (!$result) {
            throw new ModelNotFoundException("Taxi service not found");
        }

        return $result;
    }

    public function paginate(int $perPage = 15, bool $activeOnly = false): LengthAwarePaginator
    {
        return $this->baseQuery(true, $activeOnly)
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getActive(bool $withRelations = false): Collection
    {
        return $this->baseQuery($withRelations, true)->get();
    }

    public function getByLocation(int $locationId, bool $activeOnly = true): Collection
    {
        return $this->baseQuery(true, $activeOnly)
            ->where('location_id', $locationId)
            ->get();
    }

    public function create(array $data): TaxiService
    {
        return DB::transaction(function () use ($data) {
            return TaxiService::create($data);
        });
    }

    public function update(int $id, array $data): TaxiService
    {
        return DB::transaction(function () use ($id, $data) {
            $service = $this->findOrFail($id);
            $service->update($data);
            return $service->fresh();
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $service = $this->findOrFail($id);
            return $service->delete();
        });
    }

    public function updateRating(int $id, float $newRating): TaxiService
    {
        return DB::transaction(function () use ($id, $newRating) {
            $service = $this->findOrFail($id);

            $service->increment('total_ratings');
            $service->update([
                'average_rating' => DB::raw(
                    "((average_rating * (total_ratings - 1)) + {$newRating}) / total_ratings"
                )
            ]);

            return $service->fresh();
        });
    }
}