<?php

namespace App\Services\Cancellation;

use App\Models\TaxiBooking;
use App\Models\Trip;
use Carbon\Carbon;

class CancellationFeeCalculator
{
    // For Taxi Bookings
    public function calculateForTaxiBooking(TaxiBooking $booking): float
    {
        return $this->calculateFee(
            $booking->driver_assigned_at,
            $booking->pickup_date_time,
            $booking->calculated_fare
        );
    }

    // For Trips
    public function calculateForTrip(Trip $trip): float
    {
        return $this->calculateFee(
            $trip->accepted_at,
            $trip->estimated_pickup_time,
            $trip->fare_estimate
        );
    }

    private function calculateFee(?Carbon $assignmentTime, Carbon $pickupTime, float $fare): float
    {
        if (!$assignmentTime) {
            return 0; // No driver assigned yet
        }

        $timeSinceAssignment = now()->diffInMinutes($assignmentTime);
        $timeUntilPickup = now()->diffInMinutes($pickupTime, false);

        return match (true) {
            // No fee if cancelled within 2 minutes of assignment
            $timeSinceAssignment <= 2 => 0,

            // Full fee if cancelled within 30 minutes of pickup
            $timeUntilPickup <= 30 => $fare * 0.5,

            // Partial fee for cancellations after grace period
            default => min($fare * 0.2, 100)
        };
    }
}