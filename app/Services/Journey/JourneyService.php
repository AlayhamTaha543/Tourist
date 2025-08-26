<?php
namespace App\Services\Journey;

use App\Models\TourImage;
use App\Models\TravelFlight;
use App\Models\Country;
use App\Models\Rating;
use App\Models\TravelBooking;
use App\Models\Favourite;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class JourneyService
{
    /**
     * Get flights grouped by arrival countries
     *
     * @param array $filters Optional filters (status, date_range, etc.)
     * @return array
     */
    public function getFlightsByArrivalCountries(array $filters = []): array
    {
        $query = TravelFlight::with([
            'arrival.city.country',
            'departure.city.country',
            'agency'
        ]);

        // Apply filters
        $this->applyFilters($query, $filters);

        $flights = $query->get();

        // Group flights by arrival country (no need for token handling here)
        $flightsByCountry = $flights->groupBy(function ($flight) {
            return $flight->arrival?->city?->country?->name ?? 'Unknown';
        })->map(function ($countryFlights, $countryName) {
            // Get average rating for flights to this country
            $averageRating = $this->getAverageRatingForCountry($countryName);

            // Get random tour image for this country
            $randomImage = $this->getRandomTourImageForCountry($countryName);
            return [
                'country' => $countryName,
                'average_rating' => $averageRating,
                'country_image' => $randomImage,
                'price' => $countryFlights->min('price'),
                'is_favorite' => false,
                'flights' => [], // Added an empty flights array to prevent "Undefined array key" error
            ];
        })->values();

        return [
            'countries' => $flightsByCountry,
            'total_countries' => $flightsByCountry->count(),
            'total_flights' => $flights->count()
        ];
    }


    /**
     * Get flights for a specific country
     *
     * @param string $countryName
     * @param array $filters
     * @return array
     */
    public function getFlightsForCountry(string $countryName, array $filters = []): array
    {
        $query = TravelFlight::with([
            'arrival.city.country',
            'departure.city.country',
            'agency'
        ])->whereHas('arrival.city.country', function ($q) use ($countryName) {
            $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($countryName) . '%']);
        });

        // Apply filters
        $this->applyFilters($query, $filters);

        $flights = $query->get();

        return [
            'country' => $countryName,
            'total_flights' => $flights->count(),
            'flights' => $flights->map(function ($flight) {
                return $this->formatFlightDataSimple($flight);
            }),
            'arrival_country_name' => $countryName, // Added arrival country name
        ];
    }

    /**
     * Get available destination countries
     *
     * @return Collection
     */
    public function getAvailableDestinations(): Collection
    {
        return TravelFlight::with('arrival.city.country')
            ->whereHas('arrival.city.country')
            ->get()
            ->pluck('arrival.city.country')
            ->unique('id')
            ->map(function ($country) {
                return [
                    'id' => $country->id,
                    'name' => $country->name,
                    'code' => $country->code,
                    'flight_count' => TravelFlight::whereHas('arrival.city.country', function ($q) use ($country) {
                        $q->where('id', $country->id);
                    })->count()
                ];
            })
            ->sortBy('name')
            ->values();
    }

    /**
     * Search flights by multiple criteria
     *
     * @param array $searchParams
     * @return array
     */
    public function searchFlights(array $searchParams): array
    {
        $query = TravelFlight::with([
            'arrival.city.country',
            'departure.city.country',
            'agency'
        ]);

        // Search by departure country
        if (!empty($searchParams['departure_country'])) {
            $query->whereHas('departure.city.country', function ($q) use ($searchParams) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchParams['departure_country']) . '%']);
            });
        }

        // Search by arrival country
        if (!empty($searchParams['arrival_country'])) {
            $query->whereHas('arrival.city.country', function ($q) use ($searchParams) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchParams['arrival_country']) . '%']);
            });
        }

        // Search by departure city
        if (!empty($searchParams['departure_city'])) {
            $query->whereHas('departure.city', function ($q) use ($searchParams) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchParams['departure_city']) . '%']);
            });
        }

        // Search by arrival city
        if (!empty($searchParams['arrival_city'])) {
            $query->whereHas('arrival.city', function ($q) use ($searchParams) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchParams['arrival_city']) . '%']);
            });
        }

        // Apply other filters
        $this->applyFilters($query, $searchParams);

        $flights = $query->get();

        // Group by arrival country if requested
        if (!empty($searchParams['group_by_country']) && $searchParams['group_by_country']) {
            return $this->getFlightsByArrivalCountries($searchParams);
        }

        return [
            'flights' => $flights->map(function ($flight) {
                return $this->formatFlightDataSimple($flight);
            }),
            'total_flights' => $flights->count()
        ];
    }

    /**
     * Apply filters to the query
     *
     * @param $query
     * @param array $filters
     */
    private function applyFilters($query, array $filters): void
    {
        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by date range
        if (!empty($filters['departure_date_from'])) {
            $query->where('departure_time', '>=', $filters['departure_date_from']);
        }

        if (!empty($filters['departure_date_to'])) {
            $query->where('departure_time', '<=', $filters['departure_date_to']);
        }

        // Filter by price range
        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // Filter by available seats
        if (!empty($filters['min_seats'])) {
            $query->where('available_seats', '>=', $filters['min_seats']);
        }

        // Filter by agency
        if (!empty($filters['agency_id'])) {
            $query->where('agency_id', $filters['agency_id']);
        }

        // Filter future flights only
        if (!empty($filters['future_only']) && $filters['future_only']) {
            $query->where('departure_time', '>', Carbon::now());
        }
    }

    /**
     * Format flight data for simple response
     *
     * @param TravelFlight $flight
     * @return array
     */
    private function formatFlightDataSimple(TravelFlight $flight): array
    {
        return [
            'id' => $flight->id,
            'flight_number' => $flight->flight_number,
            'departure' => $flight->departure?->city?->country?->code ?? 'Unknown',
            'departure_time' => $flight->departure_time,
            'arrival'=> $flight->arrival?->city?->country?->code ?? 'Unknown',
            'arrival_time'=>$flight->arrival_time,
            'duration_minutes' => $flight->duration_minutes,
            'duration_formatted' => $flight->duration_minutes ? $this->formatDuration($flight->duration_minutes) : null,
            'price' => $flight->price,
            'available_seats' => $flight->available_seats,
            'status' => $flight->status
        ];
    }

    /**
     * Get average rating for flights to a specific country
     *
     * @param string $countryName
     * @return float|null
     */
    private function getAverageRatingForCountry(string $countryName): ?float
    {
        try {
            // Get all flight bookings to this country
            $flightIds = TravelFlight::whereHas('arrival.city.country', function ($q) use ($countryName) {
                $q->where('name', $countryName);
            })->pluck('id');

            if ($flightIds->isEmpty()) {
                return 1.5;
            }

            // Get booking IDs for flights to this country
            $bookingIds = TravelBooking::whereIn('flight_id', $flightIds)->pluck('id');

            if ($bookingIds->isEmpty()) {
                return 2.5;
            }

            // Get average rating from ratings related to these bookings
            $averageRating = Rating::whereIn('booking_id', $bookingIds)
                // ->where('is_visible', true) // Only include visible ratings
                ->avg('rating');

            return $averageRating ? round($averageRating, 1) : 1.8;

        } catch (\Exception $e) {
            // \Log::warning('Failed to get average rating for country: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get random tour image for a specific country
     *
     * @param string $countryName
     * @return string|null
     */
    private function getRandomTourImageForCountry(string $countryName): ?string
    {
        try {
            // Get a random tour image from tours in this country
            $tourImage = TourImage::whereHas('tour.location.city.country', function ($q) use ($countryName) {
                $q->where('name', $countryName);
            })
                ->inRandomOrder()
                ->first();
            $default = "images/countries/default.png";
            return $tourImage ? asset('storage/' . $tourImage->image) : asset('storage/' . $default);

        } catch (\Exception $e) {
            // \Log::warning('Failed to get random tour image for country: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Format duration in hours and minutes
     *
     * @param int $minutes
     * @return string
     */
    private function formatDuration(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return $mins > 0 ? "{$hours}h {$mins}m" : "{$hours}h";
        }

        return "{$mins}m";
    }

    /**
     * Get details for a specific country
     *
     * @param string $countryName
     * @return array|null
     */
    public function getCountryDetails(string $countryName): ?array
    {
        $country = Country::where('name', $countryName)
            ->orWhere('code', $countryName)
            ->first();

        if (!$country) {
            return null;
        }

        return [
            'id' => $country->id,
            'name' => $country->name,
            'code' => $country->code,
            'continent_code' => $country->continent_code,
            'phone_code' => $country->phone_code,
            'is_active' => $country->is_active,
            'language' => $country->language,
            'currency' => $country->currency,
            'description' => $country->description,
            'average_rating' => (string) $this->getAverageRatingForCountry($country->name),
            'price' => TravelFlight::whereHas('arrival.city.country', function ($q) use ($country) {
                $q->where('id', $country->id);
            })->min('price'),
            'is_favorite' => Auth::check() ? $this->isCountryFavorite($country->name) : false,
        ];
    }

    /**
     * Check if country is favorite for authenticated user
     */
    private function isCountryFavorite(string $countryName): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        $country = Country::where('name', $countryName)->first();

        if (!$country) {
            $country = Country::whereRaw('LOWER(name) = ?', [strtolower($countryName)])->first();
            if (!$country) {
                return false;
            }
        }

        return Favourite::where('user_id', $user->id)
            ->where('favoritable_id', $country->id)
            ->where('favoritable_type', Country::class)
            ->exists();
    }
}