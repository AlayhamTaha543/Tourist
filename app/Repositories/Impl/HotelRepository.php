<?php
namespace App\Repositories\Impl;

use App\Http\Requests\Hotel\HotelBookingRequest;
use App\Models\Booking;
use App\Models\Favourite;
use App\Models\Hotel;
use App\Models\HotelBooking;
use App\Models\Policy;
use App\Models\Promotion;
use App\Models\RoomAvailability;
use App\Models\RoomType;
use App\Repositories\Interfaces\HotelInterface;
use App\Traits\ApiResponse;
use App\Traits\HandlesUserPoints;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class HotelRepository implements HotelInterface
{
    use ApiResponse,HandlesUserPoints;

    public function showHotel($id){
        $hotel = Hotel::with('images','roomTypes')
                    ->where('id',$id)
                        ->first();
       if (!$hotel) {
             return $this->error('Hotel not found', 404);
      }
      $user = Auth::user();

            $isFavourited = false;
            if ($user) {
                $isFavourited = Favourite::where([
                    'user_id' => $user->id,
                    'favoritable_id' => $hotel->id,
                    'favoritable_type' => Hotel::class,
                ])->exists();
            }
            $now = now();
            $promotion = Promotion::where('is_active', true)
                ->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->where('applicable_type', 2)
                ->first();
                $roomsData = [];
                foreach ($hotel->roomTypes as $room) {
                    $roomsData[] = [
                        'name' => $room->name,
                        'number' => $room->number,
                    ];
                }
            $policies = Policy::where('service_type', 1)->get()->map(function ($policy) {
                return [
                    'policy_type' => $policy->policy_type,
                    'cutoff_time' => $policy->cutoff_time,
                    'penalty_percentage' => $policy->penalty_percentage,
                ];
            });
      return $this->success('Store retrieved successfully', [
        'hotel ' => $hotel,
        'image'=>$hotel->images,
        'rooms'=>$roomsData,
        'total_ratings'=>$hotel->total_ratings,
        'is_favourited' => $isFavourited,
        'promotion' => $promotion ? [
            'promotion_code' => $promotion->promotion_code,
            'description' => $promotion->description,
            'discount_type' => $promotion->discount_type,
            'discount_value' => $promotion->discount_value,
            'minimum_purchase' => $promotion->minimum_purchase,
        ] : null,
          'policies' => $policies,
    ]);
    }

    public function showAllHotel(){
        $hotels = Hotel::with('images','roomTypes')->get();

        $result = $hotels->map(function($hotel) {
            $user = Auth::user();
            $isFavourited = false;
            if ($user) {
                $isFavourited = Favourite::where([
                    'user_id' => $user->id,
                    'favoritable_id' => $hotel->id,
                    'favoritable_type' => Hotel::class,
                ])->exists();
            }
            $now = now();
            $promotion = Promotion::where('is_active', true)
                ->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->where('applicable_type', 2)
                ->first();
            $roomsData = [];
            foreach ($hotel->roomTypes as $room) {
                    $roomData = [
                        'name' => $room->name,
                        'number' => $room->number,
                        'price' => $room->price,
                    ];
                    $roomsData[] = $roomData;
            }
            return [
                'hotel' => $hotel,
                'images' => $hotel->images,
                'rooms' => $roomsData,
                'is_favourited' => $isFavourited,
                'promotion' => $promotion ? [
                    'promotion_code' => $promotion->promotion_code,
                    'description' => $promotion->description,
                    'discount_type' => $promotion->discount_type,
                    'discount_value' => $promotion->discount_value,
                    'minimum_purchase' => $promotion->minimum_purchase,
                ] : null,
            ];
        });

        return $this->success('All hotels retrieved successfully', [
            'hotels' => $result,
        ]);
    }

    public function showNearByHotel(Request $request)
    {
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $radius = 5;

        $hotels = Hotel::selectRaw("*,
            (6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            )) AS distance", [$latitude, $longitude, $latitude])
        ->having("distance", "<=", $radius)
        ->orderBy("distance")
        ->get();

        return $this->success('Nearby Hotels retrieved successfully', [
            'hotels' => $hotels,
        ]);
    }
    public function showAviableRoom($id){
        $hotel=Hotel::with('roomTypes')->where('id',$id)->first();
        if (!$hotel) {
            return $this->error('hotel not found', 404);
        }
        $hotelRoom=0;
        foreach($hotel->roomTypes as $roomType){
            $hotelRoom+=$roomType->number;
        }
        $bookingRoom = HotelBooking::where('hotel_id', $hotel->id)
        ->whereDate('check_in_date', '<=', now()->toDateString())
        ->whereDate('check_out_date', '>', now()->toDateString())
        ->count();

        if($bookingRoom < $hotelRoom){
            $aviableRoom=$hotelRoom-$bookingRoom;
        }else{
            $aviableRoom=0;
        }

        return $this->success('Nearby Hotels retrieved successfully', [
            'aviableRoom' => $aviableRoom,
        ]);
    }
    public function showAviableRoomType($id,Request $request){
        $hotel=Hotel::with('roomTypes')->where('id',$id)->first();
        if (!$hotel) {
            return $this->error('hotel not found', 404);
        }
        $hotelRoom=RoomType::where([
            'name'=>$request->room_type,
            'hotel_id'=>$hotel->id
            ])->first();
        if (!$hotelRoom) {
            return $this->error('Room type not found', 404);
        }
        $totalRooms = $hotelRoom->number;
        $bookingRoom=HotelBooking::where([
            'check_in_date'=>now()->toDateString(),
            'room_type_id'=>$hotelRoom->id
            ])->count();

        if($bookingRoom < $totalRooms){
            $aviableRoom=$totalRooms-$bookingRoom;
        }else{
            $aviableRoom=0;
        }

        return $this->success('Nearby Hotels retrieved successfully', [
            'aviableRoom' => $aviableRoom,
        ]);
    }
    public function bookHotel($id, HotelBookingRequest $request)
    {
        $hotel = Hotel::with('roomTypes')->find($id);
        if (!$hotel) return $this->error('Hotel not found', 404);

        $roomType = RoomType::where([
            'id' => $request->room_type_id,
            'hotel_id' => $hotel->id
        ])->first();

        if (!$roomType) return $this->error('Room type not found', 404);

        $check_in_date = Carbon::parse($request->check_in_date);
        $check_out_date = $check_in_date->copy()->addDays($request->number_of_days);
        $dates = [];
        for ($date = $check_in_date->copy(); $date < $check_out_date; $date->addDay()) {
            $dates[] = $date->toDateString();
        }

        $availability = RoomAvailability::where('room_type_id', $roomType->id)
            ->whereIn('date', $dates)
            // ->where('is_blocked', false)
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->date)->toDateString();
            });

        foreach ($dates as $date) {
            if (!isset($availability[$date])) {
                logger()->error("Date $date not found in availability");

                $todayPlusMonth = now()->addMonth()->toDateString();
                if ($date > $todayPlusMonth) {
                    return $this->error("You can't make a reservation more than a month in advance", 400);
                }
                return $this->error("No availability set for date $date", 400);
            }

            if ($availability[$date]->available_rooms < $request->number_of_rooms) {
                logger()->error("Not enough rooms on $date: requested {$request->number_of_rooms}, available {$availability[$date]->available_rooms}");
                return $this->error("Not enough rooms available on $date", 400);
            }
        }

        $totalCost = 0;
        foreach ($dates as $date) {
            $price = $availability[$date]->price ?? $roomType->base_price;
            $totalCost += $price * $request->number_of_rooms;
        }


        $promotion = null;
    $promotionCode = $request->promotion_code;

    if ($promotionCode) {
        $promotion = Promotion::where('promotion_code', $promotionCode)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where(function ($q) {
                $q->where('applicable_type', 1)->orWhere('applicable_type', 3);
            })
            ->first();

        if (!$promotion || !$promotion->is_active) {
            return $this->error('Invalid or expired promotion code', 400);
        }

        if ($totalCost < $promotion->minimum_purchase) {
            return $this->error('Total does not meet minimum purchase requirement', 400);
        }
    }

    $discountAmount = 0;
    if ($promotion) {
        $discountAmount = $promotion->discount_type == 1
            ? ($totalCost * $promotion->discount_value / 100)
            : $promotion->discount_value;

        $discountAmount = min($discountAmount, $totalCost);
    }

    $totalAfterDiscount = $totalCost - $discountAmount;

        $booking = Booking::create([
            'booking_reference' => 'HB-' . strtoupper(uniqid()),
            'user_id' => auth('sanctum')->id(),
            'booking_type' => 2,
            'total_price' => $totalAfterDiscount,
            'discount_amount' => $discountAmount,
            'payment_status' => 1,
        ]);

        $RoomReservation = HotelBooking::create([
            'user_id' => auth('sanctum')->id(),
            'hotel_id' => $hotel->id,
            'room_type_id' => $request->room_type_id,
            'hotel_room' => 1,
            'check_in_date' => $check_in_date,
            'check_out_date' => $check_out_date,
            'number_of_guests' => $request->number_of_guests,
            'number_of_rooms' => $request->number_of_rooms,
            'booking_id' => $booking->id,
            'cost' => $totalAfterDiscount,
        ]);

        foreach ($dates as $date) {
            $availability[$date]->decrement('available_rooms', $request->number_of_rooms);
        }

        if ($promotion) {
            $promotion->increment('current_usage');
        }

        $this->addPointsFromAction(auth('sanctum')->user(), 'book_hotel', 1);

        return $this->success('Hotel booked successfully', [
            'booking_reference' => $booking->booking_reference,
            'check_in_date' => $check_in_date,
            'check_out_date' => $check_out_date,
            'room_type' => $roomType->id,
            'cost' => $totalAfterDiscount,
            'discount_amount' => $discountAmount,
        ]);
    }
}
