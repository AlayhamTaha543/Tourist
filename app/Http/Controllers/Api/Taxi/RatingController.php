<?php

namespace App\Http\Controllers\Api\Taxi;

use App\Http\Controllers\Controller;
use App\Http\Resources\RatingResource;
use App\Services\Rating\RatingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\RatingAlreadyExistsException;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    protected $ratingService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\Rating\RatingService $ratingService
     * @return void
     */
    public function __construct(RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
        $this->middleware('auth:sanctum');
    }

    /**
     * Create a new driver rating.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'nullable|string|max:500'
        ]);

        try {
            $userId = $request->user()->id;
            $rating = $this->ratingService->createDriverRating(
                $userId,
                $request->input('driver_id'),
                $request->input('booking_id'),
                $request->input('rating'),
                $request->input('comment')
            );

            return response()->json([
                'message' => 'Rating created successfully.',
                'data' => new RatingResource($rating)
            ], 201);
        } catch (RatingAlreadyExistsException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create rating: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create rating.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a rating.
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $userId = Auth::id();

            // Verify the rating exists and belongs to the user
            $rating = $this->ratingService->findById($id);

            if ($rating->user_id !== $userId) {
                return response()->json([
                    'message' => 'Unauthorized.'
                ], 403);
            }

            $result = $this->ratingService->deleteRating($id);

            return response()->json([
                'message' => 'Rating deleted successfully.',
                'success' => $result
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Rating not found.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to delete rating: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to delete rating.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get driver ratings.
     * @param int $driverId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDriverRatings(int $driverId, Request $request): JsonResponse
    {
        try {
            $perPage = $request->input('per_page', 15);
            $includeHidden = $request->input('include_hidden', false);

            // Verify the driver exists
            $driver = app(\App\Services\Driver\DriverService::class)->getDriverById($driverId);

            $ratings = $this->ratingService->getDriverRatings(
                $driver,
                $perPage,
                $includeHidden
            );

            return response()->json([
                'data' => RatingResource::collection($ratings),
                'meta' => [
                    'current_page' => $ratings->currentPage(),
                    'last_page' => $ratings->lastPage(),
                    'per_page' => $ratings->perPage(),
                    'total' => $ratings->total()
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Driver not found.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to get driver ratings: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to get driver ratings.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get driver average rating.
     * @param int $driverId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDriverAverage(int $driverId): JsonResponse
    {
        try {
            // Verify the driver exists
            $driver = app(\App\Services\Driver\DriverService::class)->getDriverById($driverId);

            $averageRating = $this->ratingService->getDriverAverage($driver);

            return response()->json([
                'data' => [
                    'driver_id' => $driverId,
                    'average_rating' => $averageRating
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Driver not found.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Failed to get driver average rating: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to get driver average rating.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
