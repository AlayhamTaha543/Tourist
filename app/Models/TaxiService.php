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
        'manager_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'average_rating' => 'float',
        'total_ratings' => 'integer',
        'deleted_at' => 'datetime'
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
        return $this->belongsTo(User::class)->withDefault();
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
}
