<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'name',
        'number',
        'description',
        'max_occupancy',
        'base_price',
        'discount_percentage',
        'size',
        'bed_type',
        'image',
        'is_active'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'id');
    }

    public function availability(): HasMany
    {
        return $this->hasMany(RoomAvailability::class, 'room_type_id', 'id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(HotelBooking::class, 'room_type_id', 'id');
    }
}