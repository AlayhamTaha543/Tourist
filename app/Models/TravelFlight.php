<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelFlight extends Model
{
    protected $fillable = [
        'agency_id',
        'flight_number',
        'departure_id',
        'arrival_id',
        'departure_time',
        'arrival_time',
        'duration_minutes',
        'price',
        'available_seats',
        'status',
    ];

    public function agency()
    {
        return $this->belongsTo(TravelAgency::class, 'agency_id');
    }
    public function travelBooking()
    {
        return $this->hasMany(TravelBooking::class, 'flight_id');
    }

    public function departure()
    {
        return $this->belongsTo(Location::class, 'departure_id');
    }

    public function arrival()
    {
        return $this->belongsTo(Location::class, 'arrival_id');
    }
}
