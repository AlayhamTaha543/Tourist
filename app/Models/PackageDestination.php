<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageDestination extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'location_id',
        'day_number',
        'description',
        'duration'
    ];

    protected $casts = [
        'day_number' => 'integer',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(TravelPackage::class, 'package_id', 'id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }
}