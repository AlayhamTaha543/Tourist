<?php

namespace App\Repositories\Impl;

use App\Http\Requests\System\FeedBackRequest;
use App\Http\Requests\System\RatingRequest;
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

    public function UserRank(){

        $user = auth('sanctum')->user();
        if (!$user) {
            return $this->error('User not authenticated', 401);
        }
        $rank = UserRank::where('user_id',$user->id)->first();
        if ($rank) {
            return $this->success('Successful', [
                'rank' => $rank->points_earned,
            ], 200);
        }
        return $this->error('You Dont Have any Points', 401);
    }
    public function discountPoints(){
        $discountActions = PointRule::select('action', 'points')->get();

        if ($discountActions->isEmpty()) {
            return $this->error('No available discount actions found', 404);
        }

        return $this->success('Available discount actions', [
            'discounts' => $discountActions->map(function ($item) {
                return [
                    'action' => $item->action,
                    'points' => $item->points,
                ];
            }),
        ], 200);
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
            'user_id'       => $user->id,
            'rating_type'   => $request['rating_type'],
            'entity_id'     => $request['entity_id'],
            'rating'        => $request['rating'],
            'comment'       => $request['comment'],
            'ratingdate'    => now(),
            'isVisible'     => true,
            'admin_response'=> null,
        ]);

        return $this->success('Rating submitted successfully');
    }
    public function submitFeedback(FeedBackRequest $request)
    {
        $request->validated($request->all());

        $feedback = FeedBack::create([
            'user_id' => Auth::id(),
            'feedback_text' => $request['feedback_text'],
            'feedback_type' => $request['feedback_type'],
            'feedback_date' => now(),
            'status' => 1
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

    public function requestTourAdmin(Request $request){

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

        $admin = Admin::where('role', 'admin')->where('section','tour')->first();
        if (!$admin) {
            return response()->json(['message' => 'No admin found'], 404);
        }

        $admin->notify(new TourAdminRequestNotification($user, $validated));

        return response()->json(['message' => 'Your request has been sent successfully.']);
    }
}
