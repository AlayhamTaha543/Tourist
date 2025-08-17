<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'location' => $this->location ? $this->location->fullName() : null,
            'image' => $this->main_image ? asset('storage/' . $this->main_image) : null,
            'rating' => $this->average_rating,
            'price' => $this->price,
            'cuisine' => $this->cuisine,
            'tables' => $this->getTablesGroupedByLocation(),
        ];
    }

    /**
     * Get tables grouped by indoor/outdoor location with counts
     *
     * @return array
     */
    private function getTablesGroupedByLocation(): array
    {
        $tablesGrouped = $this->tables->groupBy('location'); // Assuming 'location' field exists in RestaurantTable model

        $result = [];

        foreach ($tablesGrouped as $location => $tables) {
            $result[$location] = [
                'location' => $location,
                'number_of_tables' => $tables->count()
            ];
        }

        // Ensure we always have indoor and outdoor keys, even if empty
        if (!isset($result['indoor'])) {
            $result['indoor'] = [
                'location' => 'indoor',
                'number_of_tables' => 0
            ];
        }

        if (!isset($result['outdoor'])) {
            $result['outdoor'] = [
                'location' => 'outdoor',
                'number_of_tables' => 0
            ];
        }

        return array_values($result);
    }
}