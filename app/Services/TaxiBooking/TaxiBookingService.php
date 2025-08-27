<?php

namespace App\Services\TaxiBooking;

use App\Events\TaxiBookingCreated;
use App\Events\TaxiBookingDriverAssigned;
use App\Exceptions\NoDriversAvailableException;
use App\Exceptions\TaxiBookingException;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Promotion;
use App\Models\TaxiBooking;
use App\Models\TaxiService;
use App\Models\VehicleType;
use App\Repositories\Impl\TaxiBookingRepository;
use App\Services\Driver\DriverService;
use App\Services\Vehicle\VehicleService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use App\Services\GeoapifyService; // Import GeoapifyService
use Illuminate\Support\Facades\DB;
use App\Traits\HandlesUserPoints;
use Illuminate\Support\Facades\Log;
use App\Models\DriverVehicleAssignment;
use App\Repositories\Impl\Driver\DriverAvailabilityRepository;

class TaxiBookingService
{
    use HandlesUserPoints;

    public function __construct(
        protected DriverService $driverService,
        protected VehicleService $vehicleService,
        protected TaxiBookingRepository $taxiBookingRepository,
        protected DriverAvailabilityRepository $driverAvailabilityRepository,
        protected GeoapifyService $geoapifyService // Inject GeoapifyService
    ) {
    }

    // Basic CRUD Operations
    public function getAllTaxiBookings(): Collection
    {
        return $this->taxiBookingRepository->all();
    }

    public function getTaxiBookingById(int $id): TaxiBooking
    {
        return $this->taxiBookingRepository->findOrFail($id);
    }

    public function getTaxiBookingsByUserId(int $userId): Collection
    {
        return $this->taxiBookingRepository->findByUser($userId);
    }

    public function createTaxiBooking(array $data): TaxiBooking
    {
        return DB::transaction(function () use ($data) {
            $this->validateBookingData($data);
            $booking = $this->taxiBookingRepository->create($data);

            return $booking;
        });
    }

    public function updateTaxiBooking(int $id, array $data): TaxiBooking
    {
        return DB::transaction(function () use ($id, $data) {
            $this->validateUpdateData($id, $data);
            return $this->taxiBookingRepository->update($id, $data);
        });
    }

    public function cancelTaxiBooking(int $id): TaxiBooking
    {
        return DB::transaction(function () use ($id) {
            $booking = $this->taxiBookingRepository->findOrFail($id);

            $this->validateCancellation($booking);
            $updated = $this->taxiBookingRepository->update($id, ['status' => 'cancelled']);

            $this->handleCancellationEffects($booking);
            return $updated;
        });
    }

    // Assignment Operations
    public function assignDriver(int $bookingId, int $driverId, ?int $vehicleId = null): TaxiBooking
    {
        return DB::transaction(function () use ($bookingId, $driverId, $vehicleId) {
            $booking = $this->taxiBookingRepository->findOrFail($bookingId);

            $this->validateDriverAssignment($booking, $driverId, $vehicleId);
            $updateData = $this->prepareDriverAssignmentData($driverId, $vehicleId);

            $updated = $this->taxiBookingRepository->update($bookingId, $updateData);
            $this->driverService->markBusy($driverId);

            return $updated;
        });
    }

    // Shared Rides Functionality
    public function findAvailableSharedRides(
        int $pickupLocationId,
        int $dropoffLocationId,
        string $pickupDateTime,
        int $passengerCount
    ): Collection {
        $this->validateSharedRideRequest($passengerCount);

        return $this->taxiBookingRepository->findAvailableSharedRides(
            $pickupLocationId,
            $dropoffLocationId,
            Carbon::parse($pickupDateTime),
            $passengerCount
        );
    }

    // Core Business Logic
    private function validateBookingData(array $data): void
    {
        if (empty($data['pickup_location_id'])) {
            throw new TaxiBookingException('Pickup location is required');
        }

        if ($data['is_shared'] && empty($data['max_additional_passengers'])) {
            throw new TaxiBookingException('Max passengers required for shared rides');
        }
    }

    private function validateDriverAssignment(
        TaxiBooking $booking,
        int $driverId,
        ?int $vehicleId
    ): void {
        // if (!$this->driverService->checkDriverAvailable($driverId)) {
        //     throw new TaxiBookingException('Driver is not available');
        // }

        // if ($vehicleId && !$this->vehicleService->isVehicleAvailable($vehicleId)) {
        //     throw new TaxiBookingException('Vehicle is not available');
        // }

        // if ($vehicleId && !$this->vehicleService->isVehicleAssignedToDriver($vehicleId, $driverId)) {
        //     throw new TaxiBookingException('Vehicle does not belong to driver');
        // }
    }

    private function prepareDriverAssignmentData(
        int $driverId,
        ?int $vehicleId
    ): array {
        $data = ['driver_id' => $driverId, 'status' => 'assigned'];

        if ($vehicleId) {
            $data['vehicle_id'] = $vehicleId;
            $data['status'] = 'vehicle_assigned';
        }

        return $data;
    }

    private function handleCancellationEffects(TaxiBooking $booking): void
    {
        if ($booking->driver_id) {
            $this->driverService->markAvailable($booking->driver_id);
        }

        // if ($booking->vehicle_id) {
        //     $this->vehicleService->markAvailable($booking->vehicle_id);
        // }
    }

    // Advanced Booking Features
    public function bookTaxi(
        int $taxiServiceId,
        string $pickupTime,
        float $pickupLat,
        float $pickupLng,
        float $dropoffLat,
        float $dropoffLng,
        int $radius,
        array $bookingDetails
    ): TaxiBooking {
        $pickupDateTime = Carbon::parse($pickupTime);
        $isImmediateBooking = $pickupDateTime->lessThanOrEqualTo(Carbon::now()->addMinutes(5));
        Log::debug('Booking type determination', [
            'pickup_datetime' => $pickupDateTime->toDateTimeString(),
            'current_time' => Carbon::now()->toDateTimeString(),
            'is_immediate_booking' => $isImmediateBooking,
            'minutes_difference' => $pickupDateTime->diffInMinutes(Carbon::now())
        ]);
        // Get route info FIRST to calculate accurate duration
        $routeInfo = $this->geoapifyService->getRoute(
            $pickupLat,
            $pickupLng,
            $dropoffLat,
            $dropoffLng,
            'drive'
        );

        if (!$routeInfo || !isset($routeInfo['time']) || !isset($routeInfo['distance'])) {
            throw new \Exception('Could not calculate route information. Please try again.');
        }

        $estimatedTripDuration = $routeInfo['time']; // Duration in seconds
        $estimatedTripDurationMinutes = round($estimatedTripDuration / 60, 2);

        // Calculate cost using the route distance
        $totalCost = $this->calculateTaxiCostWithDistance(
            $taxiServiceId,
            $routeInfo['distance'], // Use distance from route
            $bookingDetails
        );

        $promotion = null;
        $promotionCode = $bookingDetails['promotion_code'] ?? null;

        if ($promotionCode) {
            $promotion = Promotion::where('promotion_code', $promotionCode)
                ->where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->where(function ($q) {
                    $q->where('applicable_type', 2)->orWhere('applicable_type', 3); // 2 for taxi, 3 for all
                })
                ->first();

            if (!$promotion || !$promotion->is_active) {
                throw new \Exception('Invalid or expired promotion code');
            }

            if ($totalCost < $promotion->minimum_purchase) {
                throw new \Exception('Total does not meet minimum purchase requirement');
            }
        }

        $discountAmount = 0;
        if ($promotion) {
            $discountAmount = $promotion->discount_type == 1
                ? ($totalCost * $promotion->discount_value / 100)
                : $promotion->discount_value;

            $discountAmount = min($discountAmount, $totalCost);
        }

        $totalAfterDiscount = $totalCost - $discountAmount;

        return DB::transaction(function () use ($isImmediateBooking, $taxiServiceId, $pickupTime, $pickupDateTime, $pickupLat, $pickupLng, $radius, $bookingDetails, $totalAfterDiscount, $discountAmount, $promotion, $estimatedTripDurationMinutes) {
            $driverId = null;
            $vehicleId = null;
            $status = 'pending'; // Default status for scheduled bookings

            if ($isImmediateBooking) {
                // CRITICAL FIX: Pass duration to availability check
                $availableDrivers = $this->driverService->getAvailableDriversForBooking(
                    $taxiServiceId,
                    $pickupTime,
                    $pickupLat,
                    $pickupLng,
                    $radius,
                    $bookingDetails['vehicle_type_id'] ?? null,
                    (int) $estimatedTripDurationMinutes // This was missing!
                );

                if ($availableDrivers->isEmpty()) {
                    throw new NoDriversAvailableException();
                }

                $nearestDriver = $availableDrivers->first();
                $driverId = $nearestDriver->id;
                $vehicleId = $nearestDriver->activeVehicle->id;

                // CRITICAL: Mark driver as busy BEFORE creating booking to prevent race conditions
                $this->driverService->markBusy($driverId);
                $status = 'confirmed';
            } else {
                // For scheduled bookings, also check availability properly
                $availableDriverVehicle = $this->findAvailableDriverVehicleForScheduled(
                    $pickupDateTime,
                    $estimatedTripDurationMinutes,
                    $bookingDetails['vehicle_type_id'] ?? null
                );

                if (!$availableDriverVehicle) {
                    throw new NoDriversAvailableException();
                }

                $driverId = $availableDriverVehicle['driver_id'];
                $vehicleId = $availableDriverVehicle['vehicle_id'];
            }

            // Create the main Booking record first
            $booking = Booking::create([
                'booking_reference' => 'TB-' . strtoupper(uniqid()),
                'user_id' => $bookingDetails['user_id'],
                'booking_type' => 'taxi',
                'total_price' => $totalAfterDiscount,
                'discount_amount' => $discountAmount,
                'payment_status' => 'pending',
            ]);

            Payment::create([
                'booking_id' => $booking->id,
                'payment_reference' => 'PAY-' . strtoupper(uniqid()),
                'amount' => $totalAfterDiscount,
                'payment_date' => now(),
                'payment_method' => 'credit_card', // or get from request
                'status' => 'completed',
            ]);

            // Create the specific TaxiBooking record with all required fields
            $taxiBooking = $this->taxiBookingRepository->create(array_merge($bookingDetails, [
                'driver_id' => $driverId,
                'vehicle_id' => $vehicleId,
                'booking_id' => $booking->id,
                'status' => $status,
                'cost' => $totalAfterDiscount,
                'duration_minutes' => $estimatedTripDurationMinutes,
                'pickup_date_time' => $pickupDateTime, // Essential for overlap checking
            ]));

            // Update promotion usage if used
            if ($promotion) {
                $promotion->increment('current_usage');
            }

            // Add points for booking
            $this->addPointsFromAction(auth('sanctum')->user(), $totalAfterDiscount, $discountAmount);

            return $taxiBooking;
        });
    }

    /**
     * Calculate taxi cost using distance from route
     */
    private function calculateTaxiCostWithDistance(
        int $taxiServiceId,
        float $distanceMeters,
        array $bookingDetails
    ): float {
        $taxiService = TaxiService::find($taxiServiceId);
        $vehicleType = VehicleType::find($bookingDetails['vehicle_type_id']);

        if (!$taxiService || !$vehicleType) {
            throw new \Exception('Taxi service or vehicle type not found for cost calculation.');
        }

        $distanceKm = $distanceMeters / 1000; // Convert meters to kilometers
        $baseFare = $taxiService->base_fare ?? 10.00;
        $perKmRate = $vehicleType->per_km_rate ?? 2.00;

        return $baseFare + ($distanceKm * $perKmRate);
    }

    /**
     * Find available driver-vehicle for scheduled bookings
     */
    private function findAvailableDriverVehicleForScheduled(
        Carbon $pickupDateTime,
        float $durationMinutes,
        ?int $vehicleTypeId = null
    ): ?array {
        Log::debug('Looking for scheduled driver-vehicle assignments', [
            'pickup_datetime' => $pickupDateTime->toDateTimeString(),
            'duration_minutes' => $durationMinutes,
            'vehicle_type_id' => $vehicleTypeId
        ]);
        $query = DriverVehicleAssignment::query()
            ->active()
            ->with(['driver', 'vehicle']);

        if ($vehicleTypeId) {
            $query->whereHas('vehicle', function ($q) use ($vehicleTypeId) {
                $q->where('vehicle_type_id', $vehicleTypeId);
            });
        }

        $assignments = $query->get();
        Log::debug('Found driver-vehicle assignments', [
            'total_assignments' => $assignments->count(),
            'assignment_ids' => $assignments->pluck('id')->toArray()
        ]);


        foreach ($assignments as $assignment) {
            // Use the repository method to check availability
            $isDriverAvailable = $this->driverAvailabilityRepository->isDriverAvailableForBooking(
                $assignment->driver_id,
                $pickupDateTime,
                (int) $durationMinutes
            );

            $isVehicleAvailable = $this->vehicleService->isVehicleAvailableForBooking(
                $assignment->vehicle_id,
                $pickupDateTime,
                (int) $durationMinutes
            );
            Log::debug('Checking assignment availability', [
                'assignment_id' => $assignment->id,
                'driver_id' => $assignment->driver_id,
                'vehicle_id' => $assignment->vehicle_id,
                'driver_available' => $isDriverAvailable,
                'vehicle_available' => $isVehicleAvailable
            ]);
            if ($isDriverAvailable && $isVehicleAvailable) {
                return [
                    'driver_id' => $assignment->driver_id,
                    'vehicle_id' => $assignment->vehicle_id
                ];
            }
            Log::debug('No available driver-vehicle combination found for scheduled booking');
        }

        return null;
    }

    public function completeBooking(int $bookingId): TaxiBooking
    {
        return DB::transaction(function () use ($bookingId) {
            $booking = $this->taxiBookingRepository->update($bookingId, [
                'status' => 'completed',
                'completed_at' => now()
            ]);

            $this->driverService->markAvailable($booking->driver_id);
            // $this->vehicleService->markAvailable($booking->vehicle_id);

            return $booking;
        });
    }

    public function getUpcomingBookings(): Collection
    {
        return $this->taxiBookingRepository->upcoming();
    }

    public function getScheduledBookings(): Collection
    {
        return $this->taxiBookingRepository->scheduled();
    }
    private function validateSharedRideRequest(int $passengerCount): void
    {
        if ($passengerCount < 2) {
            throw new TaxiBookingException(
                'Shared rides require at least 2 passengers'
            );
        }

        if ($passengerCount > config('taxi.max_shared_passengers')) {
            throw new TaxiBookingException(
                'Shared rides cannot exceed ' .
                config('taxi.max_shared_passengers') . ' passengers'
            );
        }
    }
    private function validateCancellation(TaxiBooking $booking): void
    {
        if ($booking->status === 'completed') {
            throw new TaxiBookingException(
                'Completed bookings cannot be cancelled'
            );
        }

        if ($booking->pickup_date_time->diffInHours(now()) < 2) {
            throw new TaxiBookingException(
                'Cancellations must be made at least 2 hours before pickup'
            );
        }

        if ($booking->driver && $booking->driver->is_on_trip) {
            throw new TaxiBookingException(
                'Cannot cancel booking with driver currently on trip'
            );
        }
    }
    private function validateUpdateData(int $bookingId, array $data): void
    {
        $booking = $this->taxiBookingRepository->findOrFail($bookingId);

        // Prevent modification of critical fields after confirmation
        if ($booking->status !== 'pending') {
            $protectedFields = [
                'taxi_service_id',
                'vehicle_type_id',
                'pickup_location_id',
                'dropoff_location_id'
            ];

            foreach ($protectedFields as $field) {
                if (array_key_exists($field, $data)) {
                    throw new TaxiBookingException(
                        "Cannot modify $field after booking confirmation"
                    );
                }
            }
        }
    }
    // 5. Add helper method to calculate taxi cost (you'll need to implement this)
    private function calculateTaxiCost(
        int $taxiServiceId,
        float $pickupLat,
        float $pickupLng,
        float $dropoffLat,
        float $dropoffLng,
        array $bookingDetails
    ): float {
        $taxiService = TaxiService::find($taxiServiceId);
        $vehicleType = VehicleType::find($bookingDetails['vehicle_type_id']);

        if (!$taxiService || !$vehicleType) {
            throw new \Exception('Taxi service or vehicle type not found for cost calculation.');
        }

        // Get real driving distance using Geoapify
        $routeInfo = $this->geoapifyService->getRoute(
            $pickupLat,
            $pickupLng,
            $dropoffLat,
            $dropoffLng,
            'drive'
        );

        if (!$routeInfo || !isset($routeInfo['distance'])) {
            throw new \Exception('Could not calculate driving distance using Geoapify.');
        }

        $distanceKm = $routeInfo['distance'] / 1000; // Geoapify returns distance in meters

        $baseFare = $taxiService->base_fare ?? 10.00;
        $perKmRate = $vehicleType->per_km_rate ?? 2.00;

        return $baseFare + ($distanceKm * $perKmRate);
    }


}
