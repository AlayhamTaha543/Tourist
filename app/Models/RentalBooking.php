<?php

namespace App\Models;

use App\Enum\RentalBookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'customer_id',
        'vehicle_id',
        'office_id',
        'pickup_date',
        'return_date',
        'daily_rate',
        'total_price',
        'status',
    ];

    protected $casts = [
        'pickup_date' => 'date',
        'return_date' => 'date',
        'status' => RentalBookingStatus::class,
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(RentalVehicle::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(RentalOffice::class);
    }
}
