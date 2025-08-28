<?php
namespace App\Repositories\Impl;

use App\Models\Rating;
use Illuminate\Database\Eloquent\Builder;

class RatingRepository
{
    public function create(array $data): Rating
    {
        return Rating::create($data);
    }

    public function find(int $ratingId): ?Rating
    {
        return Rating::find($ratingId);
    }

    public function existsForBookingAndRateable(
        int $userId,
        ?int $bookingId, // Make bookingId nullable
        string $rateableType,
        int $rateableId
    ): bool {
        $query = Rating::where([
            'user_id' => $userId,
            'rateable_type' => $rateableType,
            'rateable_id' => $rateableId
        ]);

        if ($bookingId !== null) {
            $query->where('booking_id', $bookingId);
        } else {
            $query->whereNull('booking_id');
        }

        return $query->exists();
    }

    public function update(int $ratingId, array $data): Rating
    {
        $rating = Rating::findOrFail($ratingId);
        $rating->update($data);
        return $rating->fresh();
    }

    public function delete(int $ratingId): bool
    {
        $rating = Rating::findOrFail($ratingId);
        return $rating->delete();
    }
}