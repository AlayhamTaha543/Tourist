<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlightType extends Model
{
    protected $fillable = [
        'flight_type',
        'price',
        'available_seats',
        'travel_flight_id',
    ];

    public function travelFlight()
    {
        return $this->belongsTo(TravelFlight::class);
    }
}
