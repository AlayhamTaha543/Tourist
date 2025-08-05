<?php

namespace App\Services\interfaces;

use App\Models\Rating;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface TripServiceInterface
{
    /**
     * Get all trips
     *
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAllTrips(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get a trip by ID
     *
     * @param int $id
     * @return \App\Models\Trip
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getTripById(int $id): Trip;

    /**
     * Get trips by user
     *
     * @param int $userId
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getTripsByUser(int $userId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get trips by driver
     *
     * @param int $driverId
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getTripsByDriver(int $driverId, array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Create a trip request
     *
     * @param array $data
     * @return \App\Models\Trip
     * @throws \Exception
     */
    public function createTripRequest(array $data): Trip;

    /**
     * Get nearby trips
     *
     * @param float $lat
     * @param float $lng
     * @param float $radius
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getNearbyTrips(float $lat, float $lng, float $radius = 5.0): Collection;

    /**
     * Accept a trip
     *
     * @param int $tripId
     * @param int $driverId
     * @return \App\Models\Trip
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function acceptTrip(int $tripId, int $driverId): Trip;

    /**
     * Start a trip
     *
     * @param int $tripId
     * @return \App\Models\Trip
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function startTrip(int $tripId): Trip;

    /**
     * Calculate fare based on vehicle type and distance
     *
     * @param int $vehicleTypeId
     * @param float $distance
     * @return float
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function calculateFare(int $vehicleTypeId, float $distance): float;

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
    public function completeTrip(int $tripId, float $distance, array $additionalData = []): Trip;

    /**
     * Cancel a trip
     *
     * @param int $tripId
     * @return \App\Models\Trip
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Exception
     */
    public function cancelTrip(int $tripId): Trip;

    /**
     * Create rating for a completed trip
     *
     * @param int $userId
     * @param int $driverId
     * @param int $bookingId
     * @param float $value
     * @param string|null $comment
     * @return \App\Models\Rating
     */
    public function createTripRating(int $userId, int $driverId, int $bookingId, float $value, ?string $comment = null): Rating;

    /**
     * Get trips by status
     *
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTripsByStatus(string $status): Collection;

    /**
     * Delete a trip permanently
     *
     * @param int $id
     * @return bool
     */
    public function deleteTripPermanently(int $id): bool;
}
