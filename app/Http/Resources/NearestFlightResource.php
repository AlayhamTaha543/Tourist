<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NearestFlightResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'arrival_country' => $this->travelBooking?->flight?->arrival?->city?->country?->name
        ];
    }
}

// Alternative version with additional safety checks
class NearestFlightResourceSafe extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $countryName = null;

        // Safe navigation through the nested relationships
        if (
            $this->travelBooking &&
            $this->travelBooking->flight &&
            $this->travelBooking->flight->arrival &&
            $this->travelBooking->flight->arrival->city &&
            $this->travelBooking->flight->arrival->city->country
        ) {
            $countryName = $this->travelBooking->flight->arrival->city->country->name;
        }

        return [
            'arrival_country' => $countryName
        ];
    }
}

// If you want to return just the string instead of an object
class NearestFlightCountryOnlyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return string|null
     */
    public function toArray(Request $request): ?string
    {
        return $this->travelBooking?->flight?->arrival?->city?->country?->name;
    }
}
