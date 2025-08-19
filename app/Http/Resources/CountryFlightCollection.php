<?php 
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CountryFlightCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'countries' => CountryFlightResource::collection($this->collection),
            'total_countries' => $this->collection->count(),
            'total_flights' => $this->additional['total_flights'] ?? 0,
        ];
    }
}