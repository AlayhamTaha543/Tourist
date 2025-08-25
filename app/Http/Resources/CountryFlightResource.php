<?php

namespace App\Http\Resources;

use App\Helper\AuthHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Country;
use App\Models\Favourite;

class CountryFlightResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Initialize auth helper
        AuthHelper::init($request);

        $data = [
            'country' => $this->resource['country'],
            'average_rating' => $this->resource['average_rating'],
            'country_image' => $this->resource['country_image'],
            'price' => $this->resource['price'],
            'is_favorite' => false, // Default to false
        ];

        // Only update is_favorite if user is authenticated
        if (AuthHelper::isAuthenticated()) {
            $data['is_favorite'] = $this->isCountryFavorite($this->resource['country']);
        }

        return $data;
    }

    /**
     * Check if country is favorite for authenticated user
     */
    private function isCountryFavorite(string $countryName): bool
    {
        $user = AuthHelper::user();

        if (!$user) {
            return false;
        }

        $country = Country::where('name', $countryName)->first();

        if (!$country) {
            $country = Country::whereRaw('LOWER(name) = ?', [strtolower($countryName)])->first();
            if (!$country) {
                return false;
            }
        }

        return Favourite::where('user_id', $user->id)
            ->where('favoritable_id', $country->id)
            ->where('favoritable_type', Country::class)
            ->exists();
    }
}
