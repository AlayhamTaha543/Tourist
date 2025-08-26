<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RentalOffice extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'rating',
        'location_id',
        'manager_id',
        'image',
        'open_time',
        'close_time'
    ];

    protected $casts = [
        'open_time' => 'datetime',
        'close_time' => 'datetime',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function manager()
    {
        return $this->belongsTo(Admin::class, 'manager_id');
    }

    public function vehicles()
    {
        return $this->hasMany(RentalVehicle::class, 'office_id');
    }
    public function vehicleCategories()
    {
        return $this->hasManyThrough(
            RentalVehicleCategory::class,
            RentalVehicle::class,
            'office_id', // Foreign key on RentalVehicle table
            'id', // Foreign key on RentalVehicleCategory table
            'id', // Local key on RentalOffice table
            'category_id' // Local key on RentalVehicle table
        )->distinct();
    }

    public function getIsClosedAttribute(): bool
    {
        $currentTime = now();
        $openTime = $this->open_time;
        $closeTime = $this->close_time;

        if (!$openTime || !$closeTime) {
            return true; // Assume closed if times are not set
        }

        // Check if current time is outside opening hours
        return $currentTime->lt($openTime) || $currentTime->gt($closeTime);
    }

    public function feedbacks()
    {
        return $this->morphMany(FeedBack::class, 'feedbackable');
    }
}
