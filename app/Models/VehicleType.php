<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    use HasFactory;

    /**
     * Get the type name attribute.
     *
     * @return string
     */
    public function getTypeNameAttribute()
    {
        return $this->name;
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'vehicle_types';

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
        'name',
        'description',
        'max_passengers',
        'price_per_km',
        'base_price',
        'image_url',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'max_passengers' => 'integer',
        'price_per_km' => 'decimal:2',
        'base_price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Get the taxi service that the vehicle type belongs to.
     */
    public function taxiService()
    {
        return $this->belongsTo(TaxiService::class, 'taxi_service_id', 'id');
    }

    /**
     * Get the vehicles for the vehicle type.
     */
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'vehicle_type_id', 'id');
    }

    /**
     * Get the taxi bookings for the vehicle type.
     */
    public function taxiBookings()
    {
        return $this->hasMany(TaxiBooking::class, 'vehicle_type_id', 'id');
    }
}
