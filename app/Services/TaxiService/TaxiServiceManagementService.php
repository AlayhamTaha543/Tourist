<?php
namespace App\Services\TaxiService;

use App\Models\TaxiService;
use App\Repositories\Impl\TaxiServiceRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TaxiServiceManagementService
{
    protected $repository;

    public function __construct(TaxiServiceRepository $repository)
    {
        $this->repository = $repository;
    }


    /**
     * Get all taxi services with optional relations
     */
    public function getAllTaxiServices(bool $withRelations = true): Collection
    {
        return $this->repository->all($withRelations);
    }

    /**
     * Get paginated taxi services
     */
    public function paginateTaxiServices(int $perPage = 15, bool $activeOnly = false): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $activeOnly);
    }

    /**
     * Get active taxi services with relations
     */
    public function getActiveTaxiServices(bool $withRelations = true): Collection
    {
        return $this->repository->getActive($withRelations);
    }

    /**
     * Get taxi services by location
     */
    public function getTaxiServicesByLocation(int $locationId, bool $activeOnly = true): Collection
    {
        return $this->repository->getByLocation($locationId, $activeOnly);
    }

    /**
     * Get taxi service by ID with relations
     *
     * @throws ModelNotFoundException
     */
    public function getTaxiServiceById(int $id, bool $withRelations = true): TaxiService
    {
        return $this->repository->findOrFail($id, $withRelations);
    }

    /**
     * Create a new taxi service
     */
    public function createTaxiService(array $data): TaxiService
    {
        return $this->repository->create($data);
    }

    /**
     * Update a taxi service
     *
     * @throws ModelNotFoundException
     */
    public function updateTaxiService(int $id, array $data): TaxiService
    {
        return $this->repository->update($id, $data);
    }

    /**
     * Delete a taxi service
     *
     * @throws ModelNotFoundException
     */
    public function deleteTaxiService(int $id): bool
    {
        return $this->repository->delete($id);
    }

    /**
     * Update taxi service rating
     *
     * @throws ModelNotFoundException
     */
    public function updateServiceRating(int $serviceId, float $newRating): TaxiService
    {
        return $this->repository->updateRating($serviceId, $newRating);
    }

    /**
     * Get complete taxi service details with all relationships
     *
     * @throws ModelNotFoundException
     */
    public function getFullServiceDetails(int $id): TaxiService
    {
        $service = $this->repository->findOrFail($id, true);

        // Load additional relationships not handled by repository
        $service->load(['vehicles', 'drivers', 'vehicleTypes']);

        return $service;
    }
}
