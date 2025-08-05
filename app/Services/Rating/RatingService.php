<?php

namespace App\Services\Rating;

use App\Exceptions\RatingAlreadyExistsException;
use App\Models\{Rating, Driver, User};
use App\Repositories\Impl\Driver\DriverProfileRepository;
use App\Repositories\Impl\RatingRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use RuntimeException;

class RatingService
{
    public function __construct(
        private RatingRepository $ratingRepository,
        private DriverProfileRepository $driverProfileRepository
    ) {}

    /**
     * Create a new rating for a driver with full validation
     *
     * @throws RatingAlreadyExistsException
     */
    public function createDriverRating(
        int $userId,
        int $driverId,
        int $bookingId,
        float $value,
        ?string $comment = null
    ): Rating {
        $this->validateRatingValue($value);
        $driver= $this->driverProfileRepository->findById($driverId);
        if ($this->ratingRepository->existsForBooking(
            $userId,
            $bookingId,
            $driverId

        )) {
            throw new RatingAlreadyExistsException('User already rated this booking');
        }

        $rating = $this->ratingRepository->create([
            'user_id' => $userId,
            'booking_id' => $bookingId,
            'rateable_id' => $driverId,
            'rating' => $value,
            'comment' => $comment
        ]);

        $this->refreshDriverCache($driver);

        return $rating;
    }

    /**
     * Update rating with flexible parameters
     */
    public function updateRating(
        Rating|int $rating,
        float $value,
        ?string $comment = null
    ): Rating {
        $this->validateRatingValue($value);

        $ratingId = $rating instanceof Rating ? $rating->id : $rating;
        $rating = $this->ratingRepository->update($ratingId, [
            'rating' => $value,
            'comment' => $comment
        ]);

        if ($rating->rateable instanceof Driver) {
            $this->refreshDriverCache($rating->rateable);
        }

        return $rating;
    }

    /**
     * Delete rating by instance or ID
     */
    public function deleteRating(int $ratingId): bool
    {
             $this->ratingRepository->find($ratingId);

        $result = $this->ratingRepository->delete($ratingId);

        return $result;
    }

    /**
     * Get paginated driver ratings with filters
     */
    public function getDriverRatings(
        Driver $driver,
        int $perPage = 15,
        bool $includeHidden = false
    ): LengthAwarePaginator {
        $query = $this->ratingRepository->getForDriver($driver)
            ->orderBy('created_at', 'desc');

        if (!$includeHidden) {
            $query->where('is_visible', true);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get cached average rating with database fallback
     */
    public function getDriverAverage(Driver $driver): float
    {
        return $this->ratingRepository->getCachedAverage($driver);
    }

    /**
     * Toggle rating visibility
     */
    public function toggleVisibility(Rating $rating): Rating
    {
        return $this->ratingRepository->update($rating->id, [
            'is_visible' => !$rating->is_visible
        ]);
    }

    /**
     * Add admin response to rating
     */
    public function addAdminResponse(Rating $rating, string $response): Rating
    {
        return $this->ratingRepository->update($rating->id, [
            'admin_response' => $response
        ]);
    }

    /**
     * Get recent ratings with optional filters
     */
    public function getRecentRatings(
        ?int $limit = 10,
        ?Driver $driver = null,
        ?User $user = null
    ): Builder {
        $query = Rating::query()
            ->with(['user', 'rateable'])
            ->latest();

        if ($driver) {
            $query->whereRateable($driver);
        }

        if ($user) {
            $query->where('user_id', $user->id);
        }

        return $query->take($limit);
    }

    /**
     * Validate rating value format
     */
    private function validateRatingValue(float $value): void
    {
        if ($value < 1 || $value > 5) {
            throw new InvalidArgumentException('Rating must be between 1 and 5');
        }
    }

    /**
     * Refresh both cache and database rating
     */
    private function refreshDriverCache(Driver $driver): void
    {
        Cache::forget("driver_rating_{$driver->id}");
        $this->driverProfileRepository->update($driver->id, [
            'rating' => $this->getDriverAverage($driver)
        ]);
    }

    /**
     * Get rating by ID
     */
    public function findById(int $ratingId): Builder
    {
        return $this->ratingRepository->find($ratingId);
    }
}
