<?php

namespace App\Models;

use App\Enum\RentalVehicleStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RentalVehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_id',
        'category_id',
        'license_plate',
        'make',
        'model',
        'year',
        'seating_capacity',
        'status',
    ];

    protected $casts = [
        'status' => RentalVehicleStatus::class,
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(RentalOffice::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(RentalVehicleCategory::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(RentalVehicleStatusHistory::class, 'vehicle_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(RentalBooking::class);
    }

    public function currentStatus(): HasOne
    {
        return $this->hasOne(RentalVehicleStatusHistory::class, 'vehicle_id')
            ->latest('changed_at');
    }
}
