<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'discount',
        'latitude',
        'longitude',
        'location_id',
        'cuisine',
        'price_range',
        'opening_time',
        'closing_time',
        'average_rating',
        'total_ratings',
        'main_image',
        'website',
        'phone',
        'email',
        'has_reservation',
        'is_active',
        'is_featured',
        'admin_id'
    ];

    protected $casts = [
        'opening_time' => 'datetime',
        'closing_time' => 'datetime',
        'average_tating' => 'decimal:2',
        'has_reservation' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];


    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(RestaurantImage::class, 'restaurant_id', 'id');
    }

    public function menuCategories(): HasMany
    {
        return $this->hasMany(MenuCategory::class, 'restaurant_id', 'id');
    }

    public function tables(): HasMany
    {
        return $this->hasMany(RestaurantTable::class, 'restaurant_id', 'id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(RestaurantBooking::class, 'restaurant_id', 'id');
    }
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}