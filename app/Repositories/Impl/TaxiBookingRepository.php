<?php

namespace App\Repositories\Impl;

use App\Models\TaxiBooking;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaxiBookingRepository
{
    /**
     * Base query with common relationships
     */
    protected function baseQuery(): Builder
    {
        return TaxiBooking::with([
            'booking.user',
            'taxiService',
            'vehicleType',
            'pickupLocation',
            'dropoffLocation',
            'driver',
            'vehicle'
        ]);
    }

    /**
     * Find all taxi bookings
     */
    public function all(array $relations = []): Collection
    {
        return $this->baseQuery()
            ->with($relations)
            ->get();
    }

    /**
     * Find taxi booking by ID
     */
    public function find(int $id): ?TaxiBooking
    {
        return $this->baseQuery()->find($id);
    }

    /**
     * Find taxi booking by ID or throw exception
     */
    public function findOrFail(int $id): TaxiBooking
    {
        return $this->baseQuery()->findOrFail($id);
    }

    /**
     * Create new taxi booking with location handling
     */
    public function create(array $data): TaxiBooking
    {
        $this->processLocationData($data);

        return TaxiBooking::create($data);
    }

    /**
     * Update taxi booking record
     */
    public function update(int $id, array $data): TaxiBooking
    {
        $this->processLocationData($data);

        $booking = TaxiBooking::findOrFail($id);
        $booking->update($data);

        return $booking->fresh();
    }

    /**
     * Delete taxi booking
     */
    public function delete(int $id): bool
    {
        return TaxiBooking::destroy($id);
    }

    /**
     * Find bookings by user ID
     */
    public function findByUser(int $userId): Collection
    {
        return $this->baseQuery()
            ->whereHas('booking', fn($q) => $q->where('user_id', $userId))
            ->get();
    }

    /**
     * Find bookings by driver ID
     */
    public function findByDriver(int $driverId): Collection
    {
        return $this->baseQuery()
            ->where('driver_id', $driverId)
            ->get();
    }

    /**
     * Find bookings by taxi service ID
     */
    public function findByTaxiService(int $taxiServiceId): Collection
    {
        return $this->baseQuery()
            ->where('taxi_service_id', $taxiServiceId)
            ->get();
    }

    /**
     * Find bookings by booking ID
     */
    public function findByBookingId(int $bookingId): Collection
    {
        return $this->baseQuery()
            ->where('booking_id', $bookingId)
            ->get();
    }

    /**
     * Find available shared rides matching criteria
     */
    public function findAvailableSharedRides(
        int $pickupLocationId,
        int $dropoffLocationId,
        Carbon $pickupTime,
        int $passengerCount
    ): Collection {
        return $this->baseQuery()
            ->where('is_shared', true)
            ->where('status', 'confirmed')
            ->where('pickup_location_id', $pickupLocationId)
            ->where('dropoff_location_id', $dropoffLocationId)
            ->whereBetween('pickup_date_time', [
                $pickupTime->copy()->subMinutes(30),
                $pickupTime->copy()->addMinutes(30)
            ])
            ->whereRaw('(passenger_count + ?) <=
                (SELECT max_capacity FROM vehicle_types WHERE id = taxi_bookings.vehicle_type_id)',
                [$passengerCount]
            )
            ->get();
    }

    /**
     * Process location data into location IDs
     */
    protected function processLocationData(array &$data): void
    {
        foreach (['pickup', 'dropoff'] as $type) {
            $locationKey = "{$type}_location";
            $idKey = "{$type}_location_id";
            $addressKey = "{$type}_address";

            // If location data is provided, create/update the location
            if (isset($data[$locationKey])) {
                $location = Location::updateOrCreate(
                    [
                        'latitude' => $data[$locationKey]['latitude'],
                        'longitude' => $data[$locationKey]['longitude']
                    ],
                    [
                        'name' => $data[$locationKey]['name'] ?? null,
                        'address' => $data[$addressKey] ?? ($data[$locationKey]['address'] ?? null),
                        'city_id' => $data[$locationKey]['city_id'] ?? null
                    ]
                );

                $data[$idKey] = $location->id;
                unset($data[$locationKey]);
            }
            // If only address is provided (without coordinates), create a minimal location
            elseif (isset($data[$addressKey]) && !isset($data[$idKey])) {
                $location = Location::create([
                    'address' => $data[$addressKey],
                    'name' => $data[$addressKey],
                    'latitude' => 0, // Default or null values
                    'longitude' => 0
                ]);

                $data[$idKey] = $location->id;
            }
        }
    }

    /**
     * Scope: Upcoming bookings
     */
    public function upcoming(): Collection
    {
        return $this->baseQuery()
            ->where('pickup_date_time', '>', now())
            ->get();
    }

    /**
     * Scope: Completed bookings
     */
    public function completed(): Collection
    {
        return $this->baseQuery()
            ->where('status', 'completed')
            ->get();
    }

    /**
     * Scope: Pending confirmation bookings
     */
    public function pending(): Collection
    {
        return $this->baseQuery()
            ->where('status', 'pending')
            ->get();
    }

    /**
     * Scope: Scheduled bookings
     */
    public function scheduled(): Collection
    {
        return $this->baseQuery()
            ->where('is_scheduled', true)
            ->where('pickup_date_time', '>', now())
            ->get();
    }
}
