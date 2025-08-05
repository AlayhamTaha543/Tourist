<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'short_description',
        'location_id',
        'duration_hours',
        'duration_days',
        'base_price',
        'discount_percentage',
        'max_capacity',
        'min_participants',
        'difficulty_level',
        'average_rating',
        'total_ratings',
        'main_image',
        'is_active',
        'is_featured',
        'admin_id'
    ];

    protected $casts = [
        'duration_hours' => 'decimal:2',
        'base_price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }
    public function package(): BelongsTo
    {
        return $this->belongsTo(TravelPackage::class, 'tour_id', 'id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(TourImage::class, 'tour_id', 'id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(TourSchedule::class, 'tour_id', 'id')->where('is_active', true);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(TourTranslation::class, 'tour_id', 'id');
    }

    public function categories()
{
    return $this->belongsToMany(
        TourCategory::class, 
        'tour_category_mapping', 
        'tour_id', 
        'category_id'
    );
}

    public function bookings(): HasMany
    {
        return $this->hasMany(TourBooking::class, 'tour_id', 'id');
    }
}