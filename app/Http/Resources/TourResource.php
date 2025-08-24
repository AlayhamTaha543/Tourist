<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class TourResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $defaultImage = "images/tour/t.png";



        return [
            'id' => $this->id,
            'name' => $this->admin->name,
            'age' => $this->admin->tourGuideSkill->age,
            'skills' => $this->admin->tourGuideSkill->skills,
            'language' => $this->language,
            'description' => $this->description,
            'location' => $this->location ? $this->location->fullName() : null,
            // 'duration_days' => $this->duration_days,
            'price' => $this->base_price,
            'discount_percentage' => $this->discount_percentage,
            'average_rating' => $this->average_rating,
            'main_image' => $this->main_image ? asset('storage/' . $this->main_image) : asset('storage/' . $defaultImage),
            'images' => $this->images->map(function ($image) {
                return [
                    'image_path' => asset('storage/' . $image->image),
                    'display_order' => $image->display_order,
                ];
            }),
            'schedules' => $this->schedules->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'tour_id' => $schedule->tour_id,
                    'start_date' => Carbon::parse($schedule->start_date)->format('Y-m-d'),
                    'end_date' => Carbon::parse($schedule->end_date)->format('Y-m-d'),
                    'start_time' => Carbon::parse($schedule->start_time)->format('H:i:s'),
                    'available_spots' => $schedule->available_spots,
                    'price' => $schedule->price,
                ];
            }),
            'reviews' => FeedbackResource::collection($this->feedbacks ?? collect()),
        ];
    }
}