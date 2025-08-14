<?php
namespace App\Services\TaxiService;

use App\Helper\CountryOfNextTrip;
use App\Http\Resources\TaxiServiceCollection;
use App\Http\Resources\TaxiServiceResource;
use App\Models\TaxiService;
use App\Models\User;
use App\Repositories\Impl\TaxiServiceRepository;
use App\Services\interfaces\TaxiServiceManagementServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class TaxiServiceManagementService implements TaxiServiceManagementServiceInterface
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
    public function showAllTaxiService(bool $nextTrip = false, ?User $user = null)
    {
        if ($nextTrip) {
            $countryName = CountryOfNextTrip::getCountryForUser($user->id);
        } else {
            $userLocation = $user->location;
            $countryName = null;
            if ($userLocation) {
                // Extract country name from location string
                $locationParts = array_map('trim', explode(',', $userLocation));
                if (count($locationParts) >= 2) {
                    $countryName = end($locationParts);
                } else {
                    $countryName = $locationParts[0];
                }
            }
        }

        // Build query with location filtering
        $taxiServicesQuery = TaxiService::with(['location.city.country']);

        // Filter by country if provided
        if ($countryName) {
            $taxiServicesQuery->whereHas('location.city.country', function ($query) use ($countryName) {
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($countryName) . '%']);
            });
        }

        $taxiServices = $taxiServicesQuery->get();

        // If no taxi services found and location was provided
        if ($taxiServices->isEmpty() && $countryName) {
            return response()->json([
                'data' => ['taxi_services' => []],
                'message' => "No taxi services found in {$countryName}",
            ], 200);
        }

        // Return collection of resources - this will use your TaxiServiceResource exactly as defined
        return TaxiServiceCollection::collection($taxiServices);
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
    public function getFullTaxiServiceDetails(int $id, bool $withRelations = true): TaxiService
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
        $service->load(['vehicles', 'vehicleTypes']);


        return $service;
    }
}