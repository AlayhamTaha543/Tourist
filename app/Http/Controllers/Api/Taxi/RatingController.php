<?php

namespace App\Http\Controllers\Api\Taxi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rating\StoreRatingRequest;
use App\Http\Resources\RatingResource;
use App\Services\Rating\RatingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\RatingAlreadyExistsException;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    protected $ratingService;

    public function __construct(RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
        $this->middleware('auth:sanctum');
    }

    public function store(StoreRatingRequest $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $rating = $this->ratingService->createRating(
                $userId,
                $request->input('booking_id'),
                $request->input('rateable_type'),
                $request->input('rateable_id'),
                $request->input('rating'),
                $request->input('comment')
            );

            return response()->json([
                'message' => 'Rating created successfully.',
                'data' => new RatingResource($rating)
            ], 201);
        } catch (RatingAlreadyExistsException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Booking not found or does not belong to user.'], 404);
        } catch (\Exception $e) {
            Log::error('Failed to create rating: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create rating.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $userId = Auth::id();

            $rating = $this->ratingService->find($id);

            if (!$rating || $rating->user_id !== $userId) {
                return response()->json([
                    'message' => 'Unauthorized or Rating not found.'
                ], 403);
            }

            $result = $this->ratingService->delete($id);

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
}