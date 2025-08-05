<?php
namespace App\Repositories\Impl;

use App\Models\Booking;
use App\Models\Favourite;
use App\Models\Hotel;
use App\Models\HotelBooking;
use App\Models\Location;
use App\Models\Promotion;
use App\Models\RoomAvailability;
use App\Models\RoomType;
use App\Repositories\Interfaces\LocationInterface;
use App\Traits\ApiResponse;
use App\Traits\HandlesUserPoints;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class LocationRepository implements LocationInterface
{
    use ApiResponse,HandlesUserPoints;
    public function showLocation($id){
        $location = Location::with('city.country')->where('id',$id)
                        ->first();
       if (!$location) {
             return $this->error('location not found', 404);
      }
      $user = Auth::user();

            $isFavourited = false;
            if ($user) {
                $isFavourited = Favourite::where([
                    'user_id' => $user->id,
                    'favoritable_id' => $location->id,
                    'favoritable_type' => Location::class,
                ])->exists();
            }

      return $this->success('Store retrieved successfully', [
        'location ' => $location,

    ]);
    }

    public function showAllLocation(){
        $locations = Location::with('city.country')->get();

        $result = $locations->map(function($location) {
            $user = Auth::user();
            $isFavourited = false;
            if ($user) {
                $isFavourited = Favourite::where([
                    'user_id' => $user->id,
                    'favoritable_id' => $location->id,
                    'favoritable_type' => Location::class,
                ])->exists();
            }
            return [
                'location' => $location,
                'is_favourite' => $isFavourited,
            ];
        });

        return $this->success('All locations retrieved successfully', [
            'locations' => $result,
        ]);
    }
    public function showAllLocationFilter(){
        $locations = Location::with('city.country')->inRandomOrder()->take(4)->get();

        $result = $locations->map(function($location) {
            $user = Auth::user();
            $isFavourited = false;
            if ($user) {
                $isFavourited = Favourite::where([
                    'user_id' => $user->id,
                    'favoritable_id' => $location->id,
                    'favoritable_type' => Location::class,
                ])->exists();
            }
            return [
                'location' => $location,
                'is_favourite' => $isFavourited,
            ];
        });

        return $this->success('All locations retrieved successfully', [
            'locations' => $result,
        ]);
    }
}
