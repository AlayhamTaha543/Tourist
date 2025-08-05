<?php

namespace App\Http\Controllers\Api\Taxi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Trip\CreateTripRequest;
use App\Http\Requests\Api\Trip\ShowTripRequest;
use App\Http\Requests\Api\Trip\CancelTripRequest;
use App\Http\Requests\Api\Trip\CompleteTripRequest;
use App\Http\Requests\Api\Trip\AcceptTripRequest;
use App\Http\Requests\Api\Trip\StartTripRequest;
use App\Http\Requests\Api\Trip\RateTripRequest;
use App\Http\Requests\Api\Trip\DeleteTripRequest;
use App\Http\Requests\Api\Trip\NearbyTripsRequest;
use App\Services\Trip\TripService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class TripController extends Controller
{
    public function __construct(
        protected TripService $tripService
    ) {
    }

    /**
     * Get all trips
     */
    public function index(): JsonResponse
    {
        try {
            $trips = $this->tripService->getAllTrips();
            return response()->json($trips);
        } catch (\Exception $e) {
            return $this->handleError($e, 'Failed to retrieve trips');
        }
    }

    /**
     * Create a new trip
     */
    public function store(CreateTripRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $trip = $this->tripService->createTripRequest($validated);
            return response()->json($trip, 201);
        } catch (\Exception $e) {
            return $this->handleError($e, 'Failed to create trip');
        }
    }

    /**
     * Get trip details
     */
    public function show(ShowTripRequest $request): JsonResponse
    {
        try {
            $trip = $this->tripService->getTripById($request->id);
            return response()->json($trip);
        } catch (\Exception $e) {
            return $this->handleError($e, 'Trip not found');
        }
    }

    /**
     * Cancel a trip
     */
    public function cancel(CancelTripRequest $request): JsonResponse
    {
        try {
            $trip = $this->tripService->cancelTrip($request->id);
            return response()->json($trip);
        } catch (\Exception $e) {
            return $this->handleError($e, 'Failed to cancel trip');
        }
    }

    /**
     * Complete a trip
     */
    public function complete(CompleteTripRequest $request): JsonResponse
    {
        try {
            $trip = $this->tripService->completeTrip(
                $request->id,
                $request->distance,
                $request->additional_data ?? []
            );

            return response()->json($trip);
        } catch (\Exception $e) {
            return $this->handleError($e, 'Failed to complete trip');
        }
    }

    /**
     * Get nearby trips
     */
    public function nearby(NearbyTripsRequest $request): JsonResponse
    {
        try {
            $trips = $this->tripService->getNearbyTrips(
                $request->lat,
                $request->lng,
                $request->radius ?? 5
            );

            return response()->json($trips);
        } catch (\Exception $e) {
            return $this->handleError($e, 'Failed to find nearby trips');
        }
    }

    /**
     * Accept a trip
     */
    public function accept(AcceptTripRequest $request): JsonResponse
    {
        try {
            $driverId = $request->user()->id;
            $trip = $this->tripService->acceptTrip($request->trip_id, $driverId);
            return response()->json($trip);
        } catch (\Exception $e) {
            return $this->handleError($e, 'Failed to accept trip');
        }
    }

    /**
     * Start a trip
     */
    public function start(StartTripRequest $request): JsonResponse
    {
        try {
            $trip = $this->tripService->startTrip($request->trip_id);
            return response()->json($trip);
        } catch (\Exception $e) {
            return $this->handleError($e, 'Failed to start trip');
        }
    }

    /**
     * Rate a trip
     */
    public function rate(RateTripRequest $request): JsonResponse
    {
        try {
            $trip = $this->tripService->getTripById($request->trip_id);

            $rating = $this->tripService->createTripRating(
                $request->user()->id,
                $trip->driver_id,
                $trip->id,
                $request->rating,
                $request->comment ?? null
            );

            return response()->json($rating, 201);
        } catch (\Exception $e) {
            return $this->handleError($e, 'Failed to create rating');
        }
    }

    /**
     * Delete a trip
     */
    public function destroy(DeleteTripRequest $request): JsonResponse
    {
        try {
            $this->tripService->deleteTripPermanently($request->id);
            return response()->json(null, 204);
        } catch (\Exception $e) {
            return $this->handleError($e, 'Failed to delete trip');
        }
    }

    /**
     * Handle exceptions consistently
     */
    private function handleError(\Exception $e, string $message): JsonResponse
    {
        $code = $e instanceof ModelNotFoundException ? 404 : 500;
        $message = $e->getMessage() !== '' ? $e->getMessage() : $message;

        Log::error($message, [
            'exception' => $e,
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'message' => $message,
            'error_code' => $e->getCode()
        ], $code);
    }
}
