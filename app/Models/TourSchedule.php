<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_id',
        'start_date',
        'end_date',
        'start_time',
        'available_spots',
        'price',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(TourBooking::class, 'schedule_id', 'id');
    }
    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'tour_activites', 'schedule_id', 'activity_id')->withTimestamps();
    }
}