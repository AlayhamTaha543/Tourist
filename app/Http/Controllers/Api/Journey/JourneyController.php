<?php

namespace App\Http\Controllers\Api\Journey;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Journey\JourneyService;
use Illuminate\Http\JsonResponse;
class JourneyController extends Controller
{

    protected JourneyService $journeyService;

    public function __construct(JourneyService $journeyService)
    {
        $this->journeyService = $journeyService;
    }

    /**
     * Get all flights grouped by arrival countries
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getFlightsByCountries(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'status',
                'departure_date_from',
                'departure_date_to',
                'min_price',
                'max_price',
                'min_seats',
                'future_only'
            ]);

            $result = $this->journeyService->getFlightsByArrivalCountries($filters);

            return response()->json([
                'success' => true,
                'message' => 'Flights grouped by countries retrieved successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve flights by countries',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get flights for a specific country
     *
     * @param Request $request
     * @param string $country
     * @return JsonResponse
     */
    public function getFlightsForCountry(Request $request, string $country): JsonResponse
    {
        try {
            $filters = $request->only([
                'status',
                'departure_date_from',
                'departure_date_to',
                'min_price',
                'max_price',
                'min_seats',
                'agency_id',
                'future_only'
            ]);

            $result = $this->journeyService->getFlightsForCountry($country, $filters);

            return response()->json([
                'success' => true,
                'message' => "Flights to {$country} retrieved successfully",
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Failed to retrieve flights for {$country}",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available destination countries
     *
     * @return JsonResponse
     */
    public function getDestinations(): JsonResponse
    {
        try {
            $destinations = $this->journeyService->getAvailableDestinations();

            return response()->json([
                'success' => true,
                'message' => 'Available destinations retrieved successfully',
                'data' => [
                    'destinations' => $destinations,
                    'total_destinations' => $destinations->count()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve destinations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search flights with multiple criteria
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function searchFlights(Request $request): JsonResponse
    {
        try {
            $searchParams = $request->only([
                'departure_country',
                'arrival_country',
                'departure_city',
                'arrival_city',
                'status',
                'departure_date_from',
                'departure_date_to',
                'min_price',
                'max_price',
                'min_seats',
                'agency_id',
                'future_only',
                'group_by_country'
            ]);

            $result = $this->journeyService->searchFlights($searchParams);

            return response()->json([
                'success' => true,
                'message' => 'Flight search completed successfully',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Flight search failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
