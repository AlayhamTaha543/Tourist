<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'city_id',
        'region',
        'is_popular'
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_popular' => 'boolean',
    ];

    public function city()
{
    return $this->belongsTo(City::class);
}

public function country()
{
    return $this->belongsTo(Country::class);
}
}