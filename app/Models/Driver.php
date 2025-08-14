<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Drivers';

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
        'admin_id',
        'taxi_service_id',
        'license_number',
        'experience_years',
        'rating',
        'is_active',
    ];

    /**
     * Get the user that owns the driver.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    /**
     * Get the taxi service that the driver belongs to.
     */
    public function taxiService()
    {
        return $this->belongsTo(TaxiService::class, 'taxi_service_id', 'id');
    }

    /**
     * Get the taxi bookings for the driver.
     */
    public function taxiBookings()
    {
        return $this->hasMany(TaxiBooking::class, 'driver_id', 'id');
    }
}
