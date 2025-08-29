<?php

namespace App\Services\Rating;

use App\Exceptions\RatingAlreadyExistsException;
use App\Models\{Rating, Booking};
use App\Repositories\Impl\RatingRepository;
use InvalidArgumentException;

class RatingService
{
    public function __construct(
        private RatingRepository $ratingRepository
    ) {
    }

    /**
     * Create a new generic rating with full validation
     *
     * @throws RatingAlreadyExistsException
     */
    public function createRating(
        int $userId,
        ?int $bookingId, // Make bookingId nullable
        string $rateableType,
        int $rateableId,
        float $value,
        ?string $comment = null
    ): Rating {
        $this->validateRatingValue($value);

        // If not an application rating and bookingId is provided, check if the booking exists and belongs to the user
        if ($rateableType !== 'App\\Models\\Application' && $bookingId !== null) {
            Booking::where('id', $bookingId)
                ->where('user_id', $userId)
                ->firstOrFail();
        }

        // Check if a rating already exists for this user, booking (if applicable), and rateable entity
        if (
            $this->ratingRepository->existsForBookingAndRateable(
                $userId,
                $bookingId, // This will be null for application ratings
                $rateableType,
                $rateableId
            )
        ) {
            throw new RatingAlreadyExistsException('User already rated this entity for this booking (if applicable).');
        }

        $rating = $this->ratingRepository->create([
            'user_id' => $userId,
            'booking_id' => $bookingId,
            'rateable_type' => $rateableType,
            'rateable_id' => $rateableId,
            'rating' => $value,
            'comment' => $comment
        ]);

        return $rating;
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
     * Find a rating by ID.
     */
    public function find(int $ratingId): ?Rating
    {
        return $this->ratingRepository->find($ratingId);
    }

    /**
     * Delete a rating.
     */
    public function delete(int $ratingId): bool
    {
        return $this->ratingRepository->delete($ratingId);
    }
}
