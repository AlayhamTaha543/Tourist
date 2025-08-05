<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TravelAgency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location_id',
        'description',
        'location_id',
        'website',
        'phone',
        'email',
        'logo',
        'is_active',
        'admin_id'
    ];


    protected $casts = [
        'is_active' => 'boolean',
    ];
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(TravelPackage::class, 'agency_id', 'id');
    }
    public function flights(): HasMany
    {
        return $this->hasMany(TravelFlight::class, 'agency_id', 'id');
    }
}