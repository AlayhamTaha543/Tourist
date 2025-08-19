<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'user_id',
        'restaurant_id',
        'restaurant_chair_id',
        'reservation_date',
        'reservation_time',
        'number_of_guests',
        'cost',
        'duration_time'
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'reservation_time' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'id');
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id', 'id');
    }

    public function restaurantChair(): BelongsTo
    {
        return $this->belongsTo(RestaurantChair::class, 'restaurant_chair_id', 'id');
    }
}
