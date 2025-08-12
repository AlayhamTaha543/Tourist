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
            'name' => $this->name,
            'location' => $this->location->fullName(),
                'average_rating' => (float) $this->average_rating,
                // 'total_ratings' => $this->total_ratings,

            'cuisine' => $this->cuisine,
            'image' => $this->main_image,
        ];
    }
}
