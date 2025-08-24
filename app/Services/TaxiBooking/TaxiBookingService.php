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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaxiBookingService
{
    public function __construct(
        protected DriverService $driverService,
        protected VehicleService $vehicleService,
        protected TaxiBookingRepository $taxiBookingRepository,

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
        int $radius,
        array $bookingDetails
    ): TaxiBooking {

        $totalCost = $this->calculateTaxiCost($taxiServiceId, $bookingDetails);
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
        $availableDrivers = $this->driverService->getAvailableDriversForBooking(
            $taxiServiceId,
            $pickupTime,
            $pickupLat,
            $pickupLng,
            $radius
        );

        if ($availableDrivers->isEmpty()) {
            throw new NoDriversAvailableException();
        }

        return DB::transaction(function () use ($availableDrivers, $bookingDetails, $totalAfterDiscount, $discountAmount, $promotion) {
            $nearestDriver = $availableDrivers->first();

            // Create the main Booking record first (like in hotel booking)
            $booking = Booking::create([
                'booking_reference' => 'TB-' . strtoupper(uniqid()), // TB for Taxi Booking
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

            // Create the specific TaxiBooking record
            $taxiBooking = $this->taxiBookingRepository->create(array_merge($bookingDetails, [
                'driver_id' => $nearestDriver->id,
                'vehicle_id' => $nearestDriver->activeVehicle->id,
                'booking_id' => $booking->id, // Link to the main booking
                'status' => 'confirmed',
                'cost' => $totalAfterDiscount,
            ]));

            $this->driverService->markBusy($nearestDriver->id);

            // Update promotion usage if used
            if ($promotion) {
                $promotion->increment('current_usage');
            }

            // Add points for booking (similar to hotel booking)
            $this->addPointsFromAction(auth('sanctum')->user(), 'book_taxi', 1);

            return $taxiBooking;
        });
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
    private function calculateTaxiCost(int $taxiServiceId, array $bookingDetails): float
    {
        // Implement your taxi pricing logic here
        // This could be based on distance, time, vehicle type, etc.
        // For example:

        $taxiService = TaxiService::find($taxiServiceId);
        $vehicleType = VehicleType::find($bookingDetails['vehicle_type_id']);

        // Basic calculation example (you'll need to adjust based on your business logic)
        $baseFare = $taxiService->base_fare ?? 10.00;
        $perKmRate = $vehicleType->per_km_rate ?? 2.00;

        // Calculate distance if you have pickup and dropoff coordinates
        $distance = $this->calculateDistance(
            $bookingDetails['pickup_location']['Latitude'],
            $bookingDetails['pickup_location']['Longitude'],
            $bookingDetails['dropoff_location']['latitude'] ?? 0,
            $bookingDetails['dropoff_location']['longitude'] ?? 0
        );

        return $baseFare + ($distance * $perKmRate);
    }

    // 6. Helper method to calculate distance between two points
    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    // 7. Add the addPointsFromAction method if you don't have it
    private function addPointsFromAction($user, $action, $points)
    {
        // Implement your points system logic here
        // This might involve creating a UserPoint record or updating user's total points
    }
}