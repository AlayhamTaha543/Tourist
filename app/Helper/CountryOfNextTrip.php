<?php

namespace App\Helper;

use App\Models\User;
use Carbon\Carbon;
use Exception;

class CountryOfNextTrip
{
    private $countryName;

    public function __construct()
    {
        $this->countryName = null;
    }

    /**
     * Get the nearest booked flight and save country name to variable
     */
    public function getNearestBookedFlight($id)
    {
        try {
            $user = User::findOrFail($id);

            $bookedFlights = $user->Bookings()
                ->where('booking_type', 'travel')
                ->whereIn('status', ['confirmed', 'pending'])
                ->with([
                    'travelBooking.flight' => function ($query) {
                        $query->select(
                            'id',
                            'flight_number',
                            'departure_id',
                            'arrival_id',
                            'departure_time',
                            'arrival_time',
                            'duration_minutes',
                            'price',
                            'status'
                        );
                    },
                    'travelBooking.flight.arrival.city.country'
                ])
                ->get();

            if ($bookedFlights->isEmpty()) {
                $this->countryName = null;
                return null;
            }

            // Filter future flights and sort by departure time
            $futureFlight = $bookedFlights
                ->filter(function ($booking) {
                    return $booking->travelBooking &&
                        $booking->travelBooking->flight &&
                        $booking->travelBooking->flight->departure_time &&
                        Carbon::parse($booking->travelBooking->flight->departure_time)->isFuture();
                })
                ->sortBy(function ($booking) {
                    return Carbon::parse($booking->travelBooking->flight->departure_time);
                })
                ->first();

            if (!$futureFlight) {
                $this->countryName = null;
                return null;
            }

            // Save the country name to the variable
            $this->countryName = $futureFlight->travelBooking?->flight?->arrival?->city?->country?->name;

            return $futureFlight;

        } catch (Exception $e) {
            $this->countryName = null;
            throw new Exception('Failed to retrieve nearest flight: ' . $e->getMessage());
        }
    }

    /**
     * Get the saved country name
     */
    public function getCountryName(): ?string
    {
        return $this->countryName;
    }

    /**
     * Set the country name manually
     */
    public function setCountryName(?string $countryName): void
    {
        $this->countryName = $countryName;
    }

    /**
     * Check if country name is available
     */
    public function hasCountryName(): bool
    {
        return !is_null($this->countryName) && !empty($this->countryName);
    }

    /**
     * Get country name for a specific user (static method)
     */
    public static function getCountryForUser($userId): ?string
    {
        $instance = new self();
        $instance->getNearestBookedFlight($userId);
        return $instance->getCountryName();
    }

    /**
     * Clear the saved country name
     */
    public function clearCountryName(): void
    {
        $this->countryName = null;
    }
}
