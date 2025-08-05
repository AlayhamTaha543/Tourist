<?php
namespace App\Repositories\Impl;

use App\Models\{Rating, Driver};
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class RatingRepository
{
    // Create a new rating for a driver
    public function create(array $data): Rating
    {
        return Rating::create($data + [
            'rateable_type' => Driver::class
        ]);
    }

    public function find(int $ratingId): Builder
    {
        return Rating::find($ratingId);
    }
    // Check if a rating exists for specific booking and driver
    public function existsForBooking(
        int $userId,
        int $bookingId,
        int $driverId
    ): bool {
        return Rating::where([
            'user_id' => $userId,
            'booking_id' => $bookingId,
            'rateable_type' => Driver::class,
            'rateable_id' => $driverId
        ])->exists();
    }

    // Get cached average rating for a driver
    public function getCachedAverage(Driver $driver): float
    {
        return cache()->remember(
            "driver_rating_{$driver->id}",
            3600,
            fn() => $driver->ratings()->avg('rating') ?? 0.00
        );
    }

    // Get all ratings for a specific driver with relationships
    public function getForDriver(Driver $driver): Builder
    {
        return Rating::whereRateable($driver)
            ->with(['user', 'booking']);
    }

    // Get all driver ratings with pagination
    public function getAllDriverRatings(int $perPage = 15): LengthAwarePaginator
    {
        return Rating::where('rateable_type', Driver::class)
            ->with(['user', 'booking', 'rateable'])
            ->latest()
            ->paginate($perPage);
    }

    // Find a specific driver rating by ID
    public function findDriverRating(int $ratingId): Rating
    {
        return Rating::where('rateable_type', Driver::class)
            ->with(['user', 'booking', 'rateable'])
            ->findOrFail($ratingId);
    }

    // Update a rating
    public function update(int $ratingId, array $data): Rating
    {
        $rating = Rating::findOrFail($ratingId);
        $rating->update($data);
        return $rating->fresh()->load(['user', 'booking']);
    }

    // Delete a rating
    public function delete(int $ratingId): bool
    {
        return Rating::destroy($ratingId);
    }

    // Additional driver-specific methods

    // Get recent ratings for a driver
    public function getRecentDriverRatings(Driver $driver, int $limit = 5): Builder
    {
        return $this->getForDriver($driver)
            ->latest()
            ->limit($limit);
    }

    // Get rating summary stats for a driver
    public function getDriverRatingStats(Driver $driver): array
    {
        return [
            'average' => $this->getCachedAverage($driver),
            'count' => $driver->ratings()->count(),
            'distribution' => $driver->ratings()
                ->selectRaw('rating, count(*) as count')
                ->groupBy('rating')
                ->pluck('count', 'rating')
        ];
    }
}