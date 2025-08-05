<?php

namespace App\Services\interfaces;

use App\Models\TaxiBooking;
use App\Models\Trip;

interface CancellationFeeCalculatorInterface
{
    /**
     * Calculate cancellation fee for a taxi booking
     *
     * @param TaxiBooking $booking
     * @return float
     */
    public function calculateForTaxiBooking(TaxiBooking $booking): float;

    /**
     * Calculate cancellation fee for a trip
     *
     * @param Trip $trip
     * @return float
     */
    public function calculateForTrip(Trip $trip): float;
}
