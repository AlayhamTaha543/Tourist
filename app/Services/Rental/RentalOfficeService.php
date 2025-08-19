<?php

namespace App\Services\Rental;

use App\Helper\CountryOfNextTrip;
use App\Http\Resources\RentalOfficeCollection;
use App\Models\RentalOffice;
use App\Models\User;
use App\Repositories\Interfaces\Rent\RentalOfficeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class RentalOfficeService
{
    public function __construct(
        protected RentalOfficeRepositoryInterface $officeRepository
    ) {
    }

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
            return $this->officeRepository->withRelations($id, [
                'location',
                'manager',
                'vehicles',
                'vehicles.category',  
            ]);
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
    public function showAllRentalOffice(bool $nextTrip = false, ?User $user = null)
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

        // If no country name, return promotional message only
        if (!$countryName) {
            return response()->json([
                'data' => ['rental_offices' => []],
                'message' => "Book a flight to see our offices in your next trip!",
            ], 200);
        }

        // Build query with location filtering
        $rentalOfficesQuery = RentalOffice::with(['location.city.country']);

        // Filter by country
        $rentalOfficesQuery->whereHas('location.city.country', function ($query) use ($countryName) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($countryName) . '%']);
        });

        $rentalOffices = $rentalOfficesQuery->get();

        // If no rental offices found and location was provided
        if ($rentalOffices->isEmpty() && $countryName) {
            return response()->json([
                'data' => ['rental_offices' => []],
                'message' => "No rental offices found in {$countryName}",
            ], 200);
        }

        // Return collection of resources - this will use your RentalOfficeResource exactly as defined
        return RentalOfficeCollection::collection($rentalOffices);
    }
}