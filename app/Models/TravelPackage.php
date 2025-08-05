<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TravelPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'agency_id',
        'name',
        'description',
        'duration_days',
        'base_price',
        'discount_percentage',
        'max_participants',
        'average_rating',
        'total_ratings',
        'main_image',
        'is_active',
        'is_featured'
    ];

    protected $casts = [
        'duration_days' => 'integer',
        'base_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'max_participants' => 'integer',
        'average_rating' => 'decimal:2',
        'total_ratings' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];
    public function agency(): BelongsTo
    {
        return $this->belongsTo(TravelAgency::class, 'agency_id', 'id');
    }
    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'id');
    }

    public function destinations(): HasMany
    {
        return $this->hasMany(PackageDestination::class, 'package_id', 'id');
    }

    public function inclusions(): HasMany
    {
        return $this->hasMany(PackageInclusion::class, 'package_id', 'id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(PackageBooking::class, 'package_id', 'id');
    }
}