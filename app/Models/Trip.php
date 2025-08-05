<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trips';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'driver_id',
        'user_id',
        'status',
        'requested_at',
        'started_at',
        'completed_at',
        'fare',
        'distance_km',
        'surge_multiplier',
        'trip_type',
        'vehicle_id',
        'pickup_location',
        'dropoff_location'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'requested_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'fare' => 'decimal:2',
        'distance_km' => 'decimal:2',
        'surge_multiplier' => 'decimal:2',
        'pickup_location' => 'point',
        'dropoff_location' => 'point'
    ];

    /**
     * Get the driver that owns the trip.
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Get the user that owns the trip.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the vehicle associated with the trip.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the taxi service associated with the trip through the driver.
     */
    public function taxiService()
    {
        return $this->hasOneThrough(TaxiService::class, Driver::class, 'id', 'id', 'driver_id', 'taxi_service_id');
    }
}
