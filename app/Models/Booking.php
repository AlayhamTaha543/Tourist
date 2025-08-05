<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;
    protected $fillable = [
        'booking_reference',
        'user_id',
        'booking_type',
        'booking_date',
        'status',
        'total_price',
        'discount_amount',
        'payment_status',
        'special_requests',
        'cancellation_reason',
        'last_updated'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'booking_date' => 'datetime',
        'last_updated' => 'datetime',
        'total_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function tourBooking(): HasOne
    {
        return $this->hasOne(TourBooking::class, 'booking_id', 'id');
    }

    public function hotelBooking(): HasOne
    {
        return $this->hasOne(HotelBooking::class, 'booking_id', 'id');
    }

    public function restaurantBooking(): HasOne
    {
        return $this->hasOne(RestaurantBooking::class, 'booking_id', 'id');
    }

    public function taxiBooking(): HasOne
    {
        return $this->hasOne(TaxiBooking::class, 'booking_id', 'id');
    }

    public function packageBooking(): HasOne
    {
        return $this->hasOne(PackageBooking::class, 'booking_id', 'id');
    }
    public function travelBooking()
    {
        return $this->hasOne(TravelBooking::class, 'booking_id');
    }
    public function RentBooking()
    {
        return $this->hasOne(RentalBooking::class, 'booking_id');
    }
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'booking_id', 'id');
    }
    public function flight()
    {
        return $this->belongsTo(TravelFlight::class);
    }
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class, 'booking_id', 'id');
    }
}