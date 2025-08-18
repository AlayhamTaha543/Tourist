<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantChair extends Model
{
    use HasFactory;

    protected $table = 'restaurant_chairs'; // Explicitly set table name

    protected $fillable = [
        'restaurant_id',
        'number',
        'location',
        'is_reservable',
        'is_active',
        'cost'
    ];

    protected $casts = [
        'is_reservable' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id', 'id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(RestaurantBooking::class, 'chair_id', 'id');
    }

    public function availability(): HasMany
    {
        return $this->hasMany(ChairAvailability::class, 'chair_id', 'id');
    }
}
