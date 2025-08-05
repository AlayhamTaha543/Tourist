<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxiBooking extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'taxi_bookings';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'booking_id',
        'taxi_service_id',
        'vehicle_type_id',
        'trip_id',
        'vehicle_id',
        'driver_id',
        'pickup_location_id',
        'dropoff_location_id',
        'pickup_date_time',
        'type_of_booking',
        'estimated_distance',
        'duration_hours',
        'return_time',
        'status',
        'is_scheduled',
        'is_shared',
        'passenger_count',
        'max_additional_passengers'
    ];

    protected $casts = [
        'pickup_date_time' => 'datetime',
        'return_time' => 'datetime',
        'estimated_distance' => 'decimal:2',
        'is_scheduled' => 'boolean',
        'is_shared' => 'boolean'
    ];

    /**
     * Get the booking that owns the taxi booking.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'id');
    }

    /**
     * Get the pickup location that owns the taxi booking.
     */
    public function pickupLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'pickup_location_id', 'id');
    }

    /**
     * Get the dropoff location that owns the taxi booking.
     */
    public function dropoffLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'dropoff_location_id', 'id');
    }

    /**
     * Get the taxi service that owns the taxi booking.
     */
    public function taxiService(): BelongsTo
    {
        return $this->belongsTo(TaxiService::class, 'taxi_service_id', 'id');
    }

    /**
     * Get the vehicle type that owns the taxi booking.
     */
    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id', 'id');
    }

    /**
     * Get the driver that owns the taxi booking.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id', 'id');
    }

    /**
     * Get the vehicle that owns the taxi booking.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id');
    }
}
