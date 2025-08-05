<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantTable extends Model
{
    use HasFactory;

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
        return $this->hasMany(RestaurantBooking::class, 'table_id', 'id');
    }
}