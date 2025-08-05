<?php

namespace App\Models;

use App\Enum\RentalVehicleStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalVehicleStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'old_status',
        'new_status',
        'changed_by_id',
    ];

    protected $casts = [
        'old_status' => RentalVehicleStatus::class,
        'new_status' => RentalVehicleStatus::class,
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(RentalVehicle::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'changed_by_id');
    }
}
