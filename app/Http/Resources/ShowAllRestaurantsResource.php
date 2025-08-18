<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShowAllRestaurantsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,

            'name' => $this->name,
            'location' => $this->location->fullName(),
            'average_rating' => (float) $this->average_rating,
            // 'total_ratings' => $this->total_ratings,

            'cuisine' => $this->cuisine,
            'image' => $this->main_image ? asset('storage/' . $this->main_image) : null,
            'popular' => $this->is_popular,
            'recommended' => $this->is_recommended,
        ];
    }
}