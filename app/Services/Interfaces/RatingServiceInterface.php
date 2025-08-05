<?php

namespace App\Services\interfaces;

use App\Models\Driver;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

interface RatingServiceInterface
{
    /**
     * Create a new rating for a driver with full validation
     *
     * @param int $userId
     * @param int $driverId
     * @param int $bookingId
     * @param float $value
     * @param string|null $comment
     * @return \App\Models\Rating
     * @throws \App\Exceptions\RatingAlreadyExistsException
     */
    public function createDriverRating(
        int $userId,
        int $driverId,
        int $bookingId,
        float $value,
        ?string $comment = null
    ): Rating;

    /**
     * Update rating with flexible parameters
     *
     * @param Rating|int $rating
     * @param float $value
     * @param string|null $comment
     * @return \App\Models\Rating
     */
    public function updateRating(
        Rating|int $rating,
        float $value,
        ?string $comment = null
    ): Rating;

    /**
     * Delete rating by instance or ID
     *
     * @param int $ratingId
     * @return bool
     */
    public function deleteRating(int $ratingId): bool;

    /**
     * Get paginated driver ratings with filters
     *
     * @param Driver $driver
     * @param int $perPage
     * @param bool $includeHidden
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getDriverRatings(
        Driver $driver,
        int $perPage = 15,
        bool $includeHidden = false
    ): LengthAwarePaginator;

    /**
     * Get cached average rating with database fallback
     *
     * @param Driver $driver
     * @return float
     */
    public function getDriverAverage(Driver $driver): float;

    /**
     * Toggle rating visibility
     *
     * @param Rating $rating
     * @return \App\Models\Rating
     */
    public function toggleVisibility(Rating $rating): Rating;

    /**
     * Add admin response to rating
     *
     * @param Rating $rating
     * @param string $response
     * @return \App\Models\Rating
     */
    public function addAdminResponse(Rating $rating, string $response): Rating;

    /**
     * Get recent ratings with optional filters
     *
     * @param int|null $limit
     * @param Driver|null $driver
     * @param User|null $user
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getRecentRatings(
        ?int $limit = 10,
        ?Driver $driver = null,
        ?User $user = null
    ): Builder;

    /**
     * Get rating by ID
     *
     * @param int $ratingId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function findById(int $ratingId): Builder;
}
