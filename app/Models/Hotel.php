<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'discount',
        'star_rating',
        'checkIn_time',
        'checkOut_time',
        'average_rating',
        'total_ratings',
        'main_image',
        'website',
        'phone',
        'email',
        'is_active',
        'is_featured',
        'admin_id',
        'location_id',
    ];

    protected $casts = [
        'checkIn_time' => 'datetime',
        'checkOut_time' => 'datetime',
        'average_rating' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(HotelImage::class, 'hotel_id', 'id');
    }

    public function roomTypes(): HasMany
    {
        return $this->hasMany(RoomType::class, 'hotel_id', 'id');
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(
            HotelAmenity::class,
            'hotel_amenity_mapping',
            'hotel_id',
            'amenity_id',
            'id',
            'id'
        );
    }

}