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
    protected $primaryKey = 'DriverID';

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
        'UserID',
        'TaxiServiceID',
        'LicenseNumber',
        'ExperienceYears',
        'Rating',
        'IsActive',
    ];

    /**
     * Get the user that owns the driver.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }

    /**
     * Get the taxi service that the driver belongs to.
     */
    public function taxiService()
    {
        return $this->belongsTo(TaxiService::class, 'TaxiServiceID', 'TaxiServiceID');
    }

    /**
     * Get the taxi bookings for the driver.
     */
    public function taxiBookings()
    {
        return $this->hasMany(TaxiBooking::class, 'DriverID', 'DriverID');
    }
}