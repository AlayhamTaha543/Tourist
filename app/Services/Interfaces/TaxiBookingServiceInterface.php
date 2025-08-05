<?php

namespace App\Services\interfaces;

use App\Models\TaxiBooking;
use Illuminate\Database\Eloquent\Collection;

interface TaxiBookingServiceInterface
{
    /**
     * Get all taxi bookings
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllTaxiBookings(): Collection;

    /**
     * Get a taxi booking by ID
     *
     * @param int $id
     * @return \App\Models\TaxiBooking
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getTaxiBookingById(int $id): TaxiBooking;

    /**
     * Get taxi bookings by user ID
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTaxiBookingsByUserId(int $userId): Collection;

    /**
     * Create a new taxi booking
     *
     * @param array $data
     * @return \App\Models\TaxiBooking
     * @throws \Exception
     */
    public function createTaxiBooking(array $data): TaxiBooking;

    /**
     * Update a taxi booking
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\TaxiBooking
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function updateTaxiBooking(int $id, array $data): TaxiBooking;

    /**
     * Cancel a taxi booking
     *
     * @param int $id
     * @return \App\Models\TaxiBooking
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function cancelTaxiBooking(int $id): TaxiBooking;

    /**
     * Assign a driver to a booking
     *
     * @param int $bookingId
     * @param int $driverId
     * @param int|null $vehicleId
     * @return \App\Models\TaxiBooking
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function assignDriver(int $bookingId, int $driverId, ?int $vehicleId = null): TaxiBooking;

    /**
     * Find available shared rides
     *
     * @param int $pickupLocationId
     * @param int $dropoffLocationId
     * @param string $pickupDateTime
     * @param int $passengerCount
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public function findAvailableSharedRides(
        int $pickupLocationId,
        int $dropoffLocationId,
        string $pickupDateTime,
        int $passengerCount
    ): Collection;

    /**
     * Book a taxi with automatic driver assignment
     *
     * @param int $taxiServiceId
     * @param string $pickupTime
     * @param float $pickupLat
     * @param float $pickupLng
     * @param int $radius
     * @param array $bookingDetails
     * @return \App\Models\TaxiBooking
     * @throws \App\Exceptions\NoDriversAvailableException
     * @throws \Exception
     */
    public function bookTaxi(
        int $taxiServiceId,
        string $pickupTime,
        float $pickupLat,
        float $pickupLng,
        int $radius,
        array $bookingDetails
    ): TaxiBooking;

    /**
     * Complete a booking
     *
     * @param int $bookingId
     * @return \App\Models\TaxiBooking
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function completeBooking(int $bookingId): TaxiBooking;

    /**
     * Get upcoming bookings
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUpcomingBookings(): Collection;

    /**
     * Get scheduled bookings
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getScheduledBookings(): Collection;
}
