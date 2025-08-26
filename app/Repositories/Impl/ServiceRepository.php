<?php

namespace App\Repositories\Impl;

use App\Http\Requests\System\FeedBackRequest;
use App\Http\Requests\System\RatingRequest;
use App\Http\Resources\FeedbackResource;
use App\Models\Admin;
use App\Models\FeedBack;
use App\Models\PointRule;
use App\Models\Promotion;
use App\Models\Rating;
use App\Models\UserRank;
use App\Notifications\TourAdminRequestNotification;
use App\Repositories\Interfaces\ServiceInterface;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class ServiceRepository implements ServiceInterface
{
    use ApiResponse;

    public function UserRank()
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $rank = UserRank::where('user_id', $user->id)->first();
        $points = $rank ? $rank->points_earned : 0;

        return $points; // Returns just the number
    }

    public function getUserPoints()
    {
        $user = auth('sanctum')->user();
        if (!$user) {
            return $this->error('User not authenticated', 401);
        }

        $rank = UserRank::where('user_id', $user->id)->first();
        $points = $rank ? $rank->points_earned : 0; // Default to 0 if no rank found

        return $points;
    }


    public function addRating(RatingRequest $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return $this->error('User not authenticated', 401);
        }

        $request->validated($request->all());

        $existing = Rating::where([
            ['user_id', '=', $user->id],
            ['rating_type', '=', $request['rating_type']],
            ['entity_id', '=', $request['entity_id']],
        ])->first();

        if ($existing) {
            return $this->error('You have already rated this booking for this entity type', 400);
        }

        Rating::create([
            'user_id' => $user->id,
            'rating_type' => $request['rating_type'],
            'entity_id' => $request['entity_id'],
            'rating' => $request['rating'],
            'comment' => $request['comment'],
            'ratingdate' => now(),
            'isVisible' => true,
            'admin_response' => null,
        ]);

        return $this->success('Rating submitted successfully');
    }

    public function getAllFeedbacks()
    {
        try {
            $feedbacks = FeedBack::with('user:id,first_name,last_name', 'feedbackable')
                ->orderBy('feedback_date', 'desc')
                ->get();

            if ($feedbacks->isEmpty()) {
                return $this->error('No feedbacks found', 404);
            }

            return $this->success('All feedbacks retrieved successfully', FeedbackResource::collection($feedbacks), 200);
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve feedbacks: ' . $e->getMessage(), 500);
        }
    }

    public function getFeedbacksByType(Request $request)
    {
        try {
            $request->validate([
                'feedbackable_type' => 'required|string',
                'feedbackable_id' => 'required|integer',
            ]);

            $feedbackableType = $request->feedbackable_type;
            $feedbackableId = $request->feedbackable_id;

            $feedbacks = FeedBack::with('user:id,first_name,last_name')
                ->where('feedbackable_type', $feedbackableType)
                ->where('feedbackable_id', $feedbackableId)
                ->orderBy('feedback_date', 'desc')
                ->get();

            if ($feedbacks->isEmpty()) {
                return $this->error('No feedbacks found for the specified type and ID', 404);
            }

            return $this->success('Feedbacks retrieved successfully', FeedbackResource::collection($feedbacks), 200);
        } catch (\Exception $e) {
            return $this->error('Failed to retrieve feedbacks by type: ' . $e->getMessage(), 500);
        }
    }
    public function submitFeedback(FeedBackRequest $request)
    {
        $validatedData = $request->validated();

        $feedback = FeedBack::create([
            'user_id' => Auth::id(),
            'feedbackable_id' => $validatedData['feedbackable_id'],
            'feedbackable_type' => $validatedData['feedbackable_type'],
            'feedback_text' => $validatedData['feedback_text'],
            'feedback_date' => now(),
            'status' => 'Unread'
        ]);

        return $this->success('Feedback submitted successfully', $feedback);
    }

    public function getAvailablePromotions()
    {
        $now = now();

        $promotions = Promotion::where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->get();

        return $this->success('Available promotions', $promotions);
    }

    public function requestTourAdmin(Request $request)
    {

        $validated = $request->validate([
            'tour_name' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'location_id' => 'nullable|exists:locations,id',
            'duration_hours' => 'nullable|numeric|min:0',
            'duration_days' => 'nullable|integer|min:0',
            'base_price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'max_capacity' => 'required|integer|min:1',
            'min_participants' => 'nullable|integer|min:1',
            'difficulty_level' => 'nullable|in:easy,moderate,difficult',
            'main_image' => 'nullable|image|max:2048',
        ]);

        $user = auth()->user();

        $admin = Admin::where('role', 'admin')->where('section', 'tour')->first();
        if (!$admin) {
            return response()->json(['message' => 'No admin found'], 404);
        }

        $admin->notify(new TourAdminRequestNotification($user, $validated));

        return response()->json(['message' => 'Your request has been sent successfully.']);
    }
}
