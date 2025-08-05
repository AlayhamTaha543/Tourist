<?php

namespace App\Services\Driver;

use App\Models\Driver;
use App\Models\Rating;
use App\Repositories\Impl\Driver\DriverAvailabilityRepository;
use App\Repositories\Interfaces\Taxi\DriverAvailabilityRepositoryInterface;
use App\Repositories\Interfaces\Taxi\DriverLocationRepositoryInterface;
use App\Repositories\Interfaces\Taxi\DriverProfileRepositoryInterface;
use App\Repositories\Interfaces\Taxi\DriverStatsRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Events\DriverLocationUpdated;
use App\Events\DriverAvailabilityChanged;
use App\Repositories\Impl\Driver\DriverLocationRepository;
use App\Repositories\Impl\Driver\DriverProfileRepository;
use App\Repositories\Impl\Driver\DriverStatsRepository;
use App\Services\Rating\RatingService;
use App\Services\Vehicle\VehicleService;
use Illuminate\Support\Carbon;

class DriverService
{
    protected $driverStatsRepository, $driverProfileRepository, $driverLocationRepository, $driverAvailabilityRepository;
    protected $ratingService;
    protected $vehicleService;


    /**
     * Create a new service instance.
     *
     * @param  $driverRepository
     * @return void
     */
    public function __construct(
        DriverStatsRepository $driverStatsRepository,
        DriverProfileRepository $driverProfileRepository,
        DriverLocationRepository $driverLocationRepository,
        DriverAvailabilityRepository $driverAvailabilityRepository,
        RatingService $ratingService,
        VehicleService $vehicleService
    ) {
        $this->driverAvailabilityRepository = $driverAvailabilityRepository;
        $this->driverStatsRepository = $driverStatsRepository;
        $this->driverLocationRepository = $driverLocationRepository;
        $this->driverProfileRepository = $driverProfileRepository;
        $this->vehicleService = $vehicleService;
        $this->ratingService = $ratingService;

    }

    /**
     * Get all drivers
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllDrivers(): Collection
    {
        return $this->driverProfileRepository->getAll();
    }

    /**
     * Get a driver by ID
     *
     * @param int $id
     * @return \App\Models\Driver
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getDriverById(int $id): Driver
    {
        return $this->driverProfileRepository->findById($id);
    }

    /**
     * Get drivers by taxi service
     *
     * @param int $taxiServiceId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDriversByTaxiService(int $taxiServiceId): array
    {
        return $this->driverProfileRepository->findByTaxiServiceId($taxiServiceId);
    }

    /**
     * Get available drivers for booking
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableDrivers(): Collection
    {
        return $this->driverAvailabilityRepository->getAvailableDrivers();
    }

    /**
     * Create a new driver
     *
     * @param array $data
     * @return \App\Models\Driver
     */
    public function createDriver(array $data): Driver
    {
        try {
            DB::beginTransaction();

            $driver = $this->driverProfileRepository->create($data);

            DB::commit();
            return $driver;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create driver: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update a driver
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateDriver(int $id, array $data): Driver
    {
        try {
            DB::beginTransaction();

            $driver = $this->driverProfileRepository->update($id, $data);

            DB::commit();
            return $driver;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update driver: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update driver rating
     *
     * @param int $id
     * @param float $rating
     * @return \App\Models\Driver
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function createDriverRating(
        int $userId,
        int $driverId,
        int $bookingId,
        float $ratingValue,
        ?string $comment = null
    ): Rating {
        try {
            // $user = $this->userRepository->findOrFail($userId);
            $driver = $this->driverProfileRepository->findById($driverId);

            return $this->ratingService->createDriverRating(
                userId: $userId,
                driverId: $driverId,
                bookingId: $bookingId,
                value: $ratingValue,
                comment: $comment
            );

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException($e->getMessage());
        } catch (\Exception $e) {
            throw new \RuntimeException('Rating creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Delete a driver
     *
     * @param int $id
     * @return bool
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function deleteDriver(int $id): bool
    {
        try {
            return $this->driverProfileRepository->delete($id);
        } catch (\Exception $e) {
            throw new \Exception('Error occurred while deleting driver: ' . $e->getMessage());
        }
    }

    /**
     * Update driver location
     *
     * @param int $driverId
     * @param float $lat
     * @param float $lng
     * @return bool
     * @throws \Exception
     */
    public function updateDriverLocation(int $driverId, float $lat, float $lng): bool
    {
        try {
            DB::beginTransaction();

            $result = $this->driverLocationRepository->updateLocation($driverId, $lat, $lng);

            // Broadcast the location update event
            event(new DriverLocationUpdated($driverId, $lat, $lng));

            DB::commit();
            return $result === true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update driver location: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mark driver as available
     *
     * @param int $driverId
     * @return bool
     * @throws \Exception
     */
    public function markAvailable(int $driverId): bool
    {

        return $this->updateDriverAvailability($driverId, 'available');
    }

    /**
     * Mark driver as busy
     *
     * @param int $driverId
     * @return bool
     * @throws \Exception
     */
    public function markBusy(int $driverId): bool
    {
        return $this->updateDriverAvailability($driverId, 'busy');
    }

    /**
     * Mark driver as offline
     *
     * @param int $driverId
     * @return bool
     * @throws \Exception
     */
    public function markOffline(int $driverId): bool
    {
        return $this->updateDriverAvailability($driverId, 'offline');
    }

    /**
     * Update driver availability status
     *
     * @param int $driverId
     * @param string $status
     * @return bool
     * @throws \Exception
     */
    private function updateDriverAvailability(int $driverId, string $status): bool
    {
        try {
            DB::beginTransaction();

            $result = $this->driverAvailabilityRepository->updateAvailability($driverId, $status);

            // Broadcast the availability change event
            event(new DriverAvailabilityChanged($driverId, $status));

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update driver availability: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get available drivers for booking
     *
     * @param int $taxiServiceId
     * @param string $bookingDateTime
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableDriversForBooking(
        int $taxiServiceId,
        string $bookingDateTime,
        float $pickupLat,
        float $pickupLng,
        int $radius,
        ?int $vehicleTypeId = null
    ): Collection {
        $bookingTime = Carbon::parse($bookingDateTime);

        $drivers = $this->driverLocationRepository->getNearbyDriversByTaxiService(
            $taxiServiceId,
            $pickupLat,
            $pickupLng, // Longitude first for POINT(lng, lat)
            $radius,
            $vehicleTypeId

        );

        return $drivers->filter(function ($driver) use ($bookingTime) {
            return $this->isDriverAvailableAtTime($driver, $bookingTime) &&
                $this->vehicleService->isVehicleAvailable($driver->activeVehicle, $bookingTime);
        });
    }

    // driver time conflicts
    protected function isDriverAvailableAtTime(Driver $driver, Carbon $bookingTime): bool
    {
        return $this->driverAvailabilityRepository->isDriverAvailableAtTime(
            $driver->id,
            $bookingTime
        );
    }
    public function getBookableNearbyDrivers(

        string $bookingDateTime,
        float $pickupLat,
        float $pickupLng,
        float $radius

    ): Collection {
        $bookingTime = Carbon::parse($bookingDateTime);

        $drivers = $this->driverLocationRepository->getNearbyDrivers(
            $pickupLat,
            $pickupLng, // Longitude first for POINT(lng, lat)
            $radius
        );

        return $drivers->filter(function ($driver) use ($bookingTime) {
            return $this->isDriverAvailableAtTime($driver, $bookingTime) &&
                $this->vehicleService->isVehicleAvailable($driver->activeVehicle, $bookingTime);
        });
    }
    public function getDriverStats(int $driverId): array
    {
        return [
            'earnings' => $this->driverStatsRepository->getEarnings(
                $driverId,
                now()->startOfDay()->format('Y-m-d H:i:s'),
                now()->endOfDay()->format('Y-m-d H:i:s')
            ),
            'trip_count' => $this->driverStatsRepository->getTripCount($driverId),
            'rating' => $this->driverStatsRepository->getRating($driverId)
        ];
    }
}
