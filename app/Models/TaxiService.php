<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxiService extends Model
{
    use SoftDeletes;

    protected $table = 'taxi_services';
    protected $fillable = [
        'name',
        'description',
        'location_id',
        'logo_url',
        'website',
        'phone',
        'email',
        'is_active',
        'manager_id',
        'open_time',
        'close_time'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'average_rating' => 'float',
        'total_ratings' => 'integer',
        'deleted_at' => 'datetime',
        'open_time' => 'datetime',
        'close_time' => 'datetime'
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    // Relationships
    public function location()
    {
        return $this->belongsTo(Location::class)->withDefault();
    }

    public function manager()
    {
        return $this->belongsTo(Admin::class)->withDefault();
    }

    public function vehicleTypes()
    {
        return $this->hasMany(VehicleType::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    public function bookings()
    {
        return $this->hasMany(TaxiBooking::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithLocation($query)
    {
        return $query->with(['location']);
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
