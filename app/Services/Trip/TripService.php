<?php

namespace App\Services\Trip;

use App\Events\TripCancelled;
use App\Events\TripRequested;
use App\Jobs\ProcessTripPayment;
use App\Models\Payment;
use App\Models\Rating;
use App\Models\Trip;
use App\Models\VehicleType;
use App\Repositories\Impl\TripRepository;
use App\Services\Cancellation\CancellationFeeCalculator;
use App\Services\Driver\DriverService;
use App\Services\Rating\RatingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;

class TripService
{
    protected $tripRepository;
    protected $driverService;
    protected $ratingService;

    /**
     * Create a new service instance.
     *
     * @param  $tripRepository
     * @param \App\Repositories\Impl\Driver\DriverProfileRepository $driverRepository
     * @return void
     */
    public function __construct(
        TripRepository $tripRepository,
        DriverService $driverService,
        RatingService $ratingService,
        protected CancellationFeeCalculator $feeCalculator
    ) {
        $this->tripRepository = $tripRepository;
        $this->driverService = $driverService;
        $this->ratingService = $ratingService;
    }

    /**
     * Get all trips
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllTrips(): Collection
    {
        return $this->tripRepository->all();
    }

    /**
     * Get a trip by ID
     *
     * @param int $id
     * @return \App\Models\Trip
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getTripById(int $id): Trip
    {
        return $this->tripRepository->findOrFail($id);
    }

    /**
     * Get trips by user
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTripsByUser(int $userId): Collection
    {
        return $this->tripRepository->getByUser($userId);
    }

    /**
     * Get trips by driver
     *
     * @param int $driverId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTripsByDriver(int $driverId): Collection
    {
        return $this->tripRepository->getByDriver($driverId);
    }

    /**
     * Create a new trip request
     *
     * @param array $data
     * @return \App\Models\Trip
     */
    public function createTripRequest(array $data): Trip
    {
        try {
            DB::beginTransaction();

            $this->validateLocationData($data);

            $data = $this->prepareTripData($data);
            $trip = $this->tripRepository->create($data);

            $this->postCreationActions($trip);

            DB::commit();
            return $trip;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create trip request: ' . $e->getMessage());
            throw $e;
        }
    }
    public function getNearbyTrips(float $lat, float $lng, int $radius = 5): Collection
    {
        return $this->tripRepository->getNearbyTrips($lat, $lng, $radius);
    }

    /**
     * Accept a trip by a driver with concurrency control
     *
     * @param int $tripId
     * @param int $driverId
     * @return \App\Models\Trip
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function acceptTrip(int $tripId, int $driverId): Trip
    {
        try {
            return DB::transaction(function () use ($tripId, $driverId) {
                // 1. Atomic lock for concurrency control using repository method
                $trip = $this->tripRepository->findPendingWithLock($tripId);

                if (!$trip) {
                    throw new ModelNotFoundException('Trip not found or already taken');
                }

                // 2. Update trip status and timestamps
                $this->tripRepository->update($tripId, [
                    'driver_id' => $driverId,
                    'status' => 'accepted',
                    'accepted_at' => now()
                ]);

                // 3. Update driver status using dedicated service
                app(DriverService::class)->markBusy($driverId);

                // 4. Get fresh instance with relations
                $updatedTrip = $this->tripRepository->findOrFail($tripId);

                // 5. Broadcast event after successful update
                event(new \App\Events\TripStatusChanged($updatedTrip));

                return $updatedTrip;
            });
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Trip not found or already taken');
        } catch (\Exception $e) {
            Log::error("Trip acceptance failed: {$e->getMessage()}", [
                'trip_id' => $tripId,
                'driver_id' => $driverId,
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Failed to accept trip: ' . $e->getMessage());
        }
    }

    /**
     * Start a trip
     *
     * @param int $tripId
     * @return \App\Models\Trip
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function startTrip(int $tripId): Trip
    {
        try {
            return DB::transaction(function () use ($tripId) {
                $trip = $this->tripRepository->findOrFail($tripId);

                // Check if trip can be started
                if ($trip->status !== 'accepted') {
                    throw new \Exception('Trip cannot be started');
                }

                // Update trip status
                $this->tripRepository->updateStatus($tripId, 'in_progress');

                // Broadcast event for real-time notification
                $updatedTrip = $this->tripRepository->findOrFail($tripId);
                event(new \App\Events\TripStatusChanged($updatedTrip));

                return $updatedTrip;
            });
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Trip not found');
        } catch (\Exception $e) {
            throw new \Exception('Error occurred while starting trip: ' . $e->getMessage());
        }
    }
    /**
     * Calculate fare based on vehicle type and distance
     *
     * @param int $vehicleTypeId
     * @param float $distance
     * @return float
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function calculateFare(int $vehicleTypeId, float $distance): float
    {
        $vehicleType = VehicleType::findOrFail($vehicleTypeId);
        return $vehicleType->base_price + ($distance * $vehicleType->price_per_km);
    }

    /**
     * Complete a trip
     *
     * @param int $tripId
     * @param float $distance
     * @param array $additionalData
     * @return \App\Models\Trip
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function completeTrip(int $tripId, float $distance, array $additionalData = []): Trip
    {
        try {
            return DB::transaction(function () use ($tripId, $distance, $additionalData) {
                $trip = $this->tripRepository->findOrFail($tripId);

                $this->tripRepository->update($tripId, ['distance_km' => $distance]);
                $this->tripRepository->calculateFare($trip);

                $tripData = array_merge([
                    'status' => 'completed',
                    'completed_at' => now(),
                ], $additionalData);

                $this->tripRepository->update($tripId, $tripData);
                $this->createPaymentRecord($trip->id, $trip->fare);
                $this->driverService->markAvailable($trip->driver_id);

                return $this->tripRepository->findOrFail($tripId);
            });
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Trip not found');
        } catch (\Exception $e) {
            Log::error('Error completing trip: ' . $e->getMessage());
            throw new \Exception('Error completing trip: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a trip
     *
     * @param int $tripId
     * @return \App\Models\Trip
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function cancelTrip(int $tripId): Trip
    {
        try {
            return DB::transaction(function () use ($tripId) {
                $trip = $this->tripRepository->findOrFail($tripId);

                // Only pending or accepted trips can be cancelled
                if (!in_array($trip->status, ['pending', 'accepted'])) {
                    throw new \Exception('Trip cannot be cancelled');
                }

                // If trip was accepted, free up the driver using the dedicated service
                if ($trip->status === 'accepted' && $trip->driver_id) {
                    app(DriverService::class)->markAvailable($trip->driver_id);
                }
                $this->validateCancellation($trip);

                $fee = $this->feeCalculator->calculateForTrip($trip);

                $tripData = [
                    'status' => 'cancelled',
                    'cancellation_fee' => $fee,
                    'cancelled_at' => now()
                ];

                $this->handleCancellationEffects($trip, $fee);

                // Update trip status
                $this->tripRepository->updateStatus($tripId, 'cancelled');

                // Broadcast event for real-time notification
                $updatedTrip = $this->tripRepository->findOrFail($tripId);
                event(new \App\Events\TripStatusChanged($updatedTrip));

                return $updatedTrip;
            });
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Trip not found');
        } catch (\Exception $e) {
            throw new \Exception('Error occurred while cancelling trip: ' . $e->getMessage());
        }
    }
    private function validateCancellation(Trip $trip): void
    {
        if (!in_array($trip->status, ['pending', 'accepted'])) {
            throw new \Exception('Trip cannot be cancelled');
        }

        if ($trip->driver && $trip->driver->is_on_trip) {
            throw new \Exception('Cannot cancel trip with active driver');
        }
    }
    protected function validateLocationData(array $data): void
    {
        if (!isset($data['pickup_lat'], $data['pickup_lng'])) {
            throw new InvalidArgumentException('Missing pickup location coordinates');
        }
    }
    /**
     * Post-creation actions for a new trip
     */
    protected function postCreationActions(Trip $trip): void
    {
        event(new TripRequested($trip));
        dispatch(new ProcessTripPayment($trip));
    }
    /**
     * Prepare trip data with default values
     */
    protected function prepareTripData(array $data): array
    {
        return array_merge($data, [
            'status' => 'pending',
            'requested_at' => now(),
            'surge_multiplier' => $data['surge_multiplier'] ?? 1.0
        ]);
    }
    /**
     * Create payment record for completed trip
     *
     * @param int $bookingId
     * @param float $amount
     * @return \App\Models\Payment
     */
    protected function createPaymentRecord(int $bookingId, float $amount): Payment
    {
        return Payment::create([
            'booking_id' => $bookingId,
            'amount' => $amount,
            'status' => 'pending',
            'payment_method' => 'system_default',
            'payment_date' => now()
        ]);
    }

    /**
     * Create rating for a completed trip
     *
     * @param int $userId
     * @param int $driverId
     * @param int $rating
     * @param string|null $comment
     * @return \App\Models\Rating
     */
    public function createTripRating(int $userId, int $driverId, int $bookingId, float $value, ?string $comment = null): Rating
    {


        $newRating = $this->ratingService->createDriverRating($userId, $driverId, $bookingId, $value, $comment);
        $this->updateDriverAverageRating($driverId);

        return $newRating;
    }

    protected function updateDriverAverageRating(int $driverId): void
    {
        $driver = $this->driverService->getDriverById($driverId);
        $averageRating = $this->ratingService->getDriverAverage($driver);

        $driver->rating = $averageRating;
        $driver->save();
    }


    public function getTripsByStatus(string $status): Collection
    {
        return $this->tripRepository->getByStatus($status);
    }

    public function deleteTripPermanently(int $id): bool
    {
        return $this->tripRepository->delete($id);
    }
    private function handleCancellationEffects(Trip $trip, float $fee): void
    {
        // Free up driver
        if ($trip->driver_id) {
            app(DriverService::class)->markAvailable($trip->driver_id);
        }

        // Process financials
        if ($fee > 0) {
            // $this->chargeUser($trip->user, $fee);
            // $this->compensateDriver($trip->driver, $fee);
        }

        // Send notifications
        event(new TripCancelled($trip, $fee));
    }
}
