<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vehicles';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'taxi_service_id',
        'vehicle_type_id',
        'registration_number',
        'model',
        'year',
        'color',
        'is_active',
    ];
    protected $casts = [
        'assigned_at' => 'datetime',
        'unassigned_at' => 'datetime',
    ];
    /**
     * Get the vehicle name attribute for Filament.
     *
     * @return string
     */
    public function getVehicleNameAttribute()
    {
        return $this->registration_number . ' - ' . $this->model . ' (' . $this->year . ')';
    }

    /**
     * Get the taxi service that the vehicle belongs to.
     */
    public function taxiService()
    {
        return $this->belongsTo(TaxiService::class, 'taxi_service_id', 'id') ;
    }

    /**
     * Get the vehicle type that the vehicle belongs to.
     */
    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id', 'id') ;
    }
    public function drivers()
{
    return $this->belongsToMany(Driver::class, 'driver_vehicle_assignments')
               ->withPivot(['assigned_at', 'unassigned_at'])
               ->withTimestamps();
}

public function activeDriver()
{
    return $this->drivers()
               ->whereNull('driver_vehicle_assignments.unassigned_at')
               ->latest('driver_vehicle_assignments.assigned_at');
}
    /**
     * Get the taxi bookings for the vehicle.
     */
    public function taxiBookings()
    {
        return $this->hasMany(TaxiBooking::class, 'vehicle_id', 'id') ;
    }
}
