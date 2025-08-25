<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelBooking extends Model
{
    protected $fillable = [
        'user_id',
        'booking_id',
        'flight_id',
        'booking_date',
        'number_of_people',
        'total_price',
        'status',
        'passport_image',
        'ticket_type',
        'discount_amount',
        'flight_type_name',
        'additional_bag_fines'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function flight()
    {
        return $this->belongsTo(TravelFlight::class);
    }
}