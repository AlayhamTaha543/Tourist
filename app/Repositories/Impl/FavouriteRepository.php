<?php
namespace App\Repositories\Impl;

use App\Models\Favourite;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\Tour;
use App\Models\TravelPackage;
use App\Models\User;
use App\Models\Country;
use App\Repositories\Interfaces\FavouriteInterface;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;

class FavouriteRepository implements FavouriteInterface
{
    use ApiResponse;

    public function showFavourite($id)
    {
        $user = auth()->user();
        $favourite = Favourite::where([
            'id' => $id,
            'user_id' => $user->id
        ])->with('favoritable')->first();

        if (!$favourite) {
            return $this->error('favourite not found', 404);
        }

        // Prepare the response data
        $responseData = [
            'user' => $user->first_name . ' ' . $user->last_name, // Adjust field names as needed
            'name' => $favourite->favoritable->name ?? $favourite->favoritable->title ?? 'Unknown', // Get name or title based on the favoritable type
            'type' => class_basename($favourite->favoritable_type),

        ];

        return $this->success('Store retrieved successfully', [
            'favourite' => $responseData,
        ]);
    }

    public function showAllFavourite()
    {
        $user = auth()->user();
        $favourites = Favourite::where('user_id', $user->id)
            ->with('favoritable')
            ->get();

        $result = $favourites->map(function ($favourite) use ($user) {
            return [
                'id' => $favourite->id,
                'user' => $user->first_name . ' ' . $user->last_name, // Adjust field names as needed
                'name' => $favourite->favoritable->name ?? $favourite->favoritable->title ?? 'Unknown', // Get name or title based on the favoritable type
                'type' => class_basename($favourite->favoritable_type),

            ];
        });

        return $this->success('Favorites retrieved successfully', [
            'favourites' => $result,
        ]);
    }
    public function addRestaurantToFavourite($id)
    {
        $user = auth()->user();
        $restaurant = Restaurant::find($id);
        if (!$restaurant) {
            return $this->error('Restaurant not found', 404);
        }
        $alreadyExists = Favourite::where([
            'user_id' => $user->id,
            'favoritable_id' => $restaurant->id,
            'favoritable_type' => Restaurant::class,
        ])->exists();

        if ($alreadyExists) {
            return $this->error('This restaurant is already in your favourites.', 400);
        }

        $favourite = Favourite::create([
            'user_id' => $user->id,
            'favoritable_id' => $restaurant->id,
            'favoritable_type' => Restaurant::class,
        ]);

        return $this->success('Restaurant added to favourites.', ['favourite' => $favourite]);
    }

    public function addHotelToFavourite($id)
    {
        $user = auth()->user();
        $hotel = Hotel::find($id);
        if (!$hotel) {
            return $this->error('hotel not found', 404);
        }
        $alreadyExists = Favourite::where([
            'user_id' => $user->id,
            'favoritable_id' => $hotel->id,
            'favoritable_type' => Hotel::class,
        ])->exists();

        if ($alreadyExists) {
            return $this->error('This hotel is already in your favourites.', 400);
        }

        $favourite = Favourite::create([
            'user_id' => $user->id,
            'favoritable_id' => $hotel->id,
            'favoritable_type' => Hotel::class,
        ]);

        return $this->success('Hotel added to favourites.', ['favourite' => $favourite]);
    }
    public function addTourToFavourite($id)
    {
        $user = auth()->user();
        $tour = Tour::find($id);
        if (!$tour) {
            return $this->error('tour not found', 404);
        }
        $alreadyExists = Favourite::where([
            'user_id' => $user->id,
            'favoritable_id' => $tour->id,
            'favoritable_type' => Tour::class,
        ])->exists();

        if ($alreadyExists) {
            return $this->error('This tour is already in your favourites.', 400);
        }

        $favourite = Favourite::create([
            'user_id' => $user->id,
            'favoritable_id' => $tour->id,
            'favoritable_type' => Tour::class,
        ]);

        return $this->success('Tour added to favourites.', ['favourite' => $favourite]);
    }
    public function addPackageToFavourite($id)
    {
        $user = auth()->user();
        $package = TravelPackage::find($id);
        if (!$package) {
            return $this->error('package not found', 404);
        }
        $alreadyExists = Favourite::where([
            'user_id' => $user->id,
            'favoritable_id' => $package->id,
            'favoritable_type' => TravelPackage::class,
        ])->exists();

        if ($alreadyExists) {
            return $this->error('This package is already in your favourites.', 400);
        }

        $favourite = Favourite::create([
            'user_id' => $user->id,
            'favoritable_id' => $package->id,
            'favoritable_type' => TravelPackage::class,
        ]);

        return $this->success('package added to favourites.', ['favourite' => $favourite]);
    }

    public function removeFromFavouriteById($id)
    {
        $user = auth()->user();

        $favourite = Favourite::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$favourite) {
            return $this->error('Favourite not found or does not belong to the user.', 404);
        }

        $favourite->delete();

        return $this->success('Removed from favourites.');
    }

    public function addCountryToFavourite($id, $isFavorite): \Illuminate\Http\JsonResponse
    {
        if ($isFavorite === true) {
            return $this->addCountryToFavouriteRequest($id);
        }

        return $this->removeCountryFromFavourite($id);
    }

    private function addCountryToFavouriteRequest($id): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $country = Country::find($id);
        if (!$country) {
            return $this->error('Country not found', 404);
        }

        $alreadyExists = Favourite::where([
            'user_id' => $user->id,
            'favoritable_id' => $country->id,
            'favoritable_type' => Country::class,
        ])->exists();

        if ($alreadyExists) {
            return $this->error('This country is already in your favourites.', 400);
        }

        $favourite = Favourite::create([
            'user_id' => $user->id,
            'favoritable_id' => $country->id,
            'favoritable_type' => Country::class,
        ]);

        return $this->success('Country added to favourites.', ['favourite' => $favourite]);
    }

    public function removeCountryFromFavourite($id): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();

        $favourite = Favourite::where([
            'user_id' => $user->id,
            'favoritable_id' => $id,
            'favoritable_type' => Country::class,
        ])->first();

        if (!$favourite) {
            return $this->error('Country not found in favourites or does not belong to the user.', 404);
        }

        $favourite->delete();

        return $this->success('Country removed from favourites.');
    }
}