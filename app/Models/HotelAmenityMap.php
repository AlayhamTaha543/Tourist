<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelAmenityMap extends Model
{
    protected $fillable=[
        'hotel_id',
        'amenity_id',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'id');
    }
    public function amenity()
    {
        return $this->belongsTo(HotelAmenity::class, 'amenity_id', 'id');
    }
}
