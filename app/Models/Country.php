<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'continent_code',
        'phone_code',
        'is_active',
        'language',
        'currency',
        'description'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'country_id', 'id');
    }
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'country_id', 'id');
    }

    public function locations(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Location::class, City::class);
    }

    public function tours(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Tour::class, Location::class, 'city_id', 'location_id', 'id', 'id');
    }

    public function departureFlights(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(TravelFlight::class, Location::class, 'city_id', 'departure_id', 'id', 'id');
    }

    public function arrivalFlights(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(TravelFlight::class, Location::class, 'city_id', 'arrival_id', 'id', 'id');
    }

    public function flights()
    {
        return $this->departureFlights->merge($this->arrivalFlights);
    }
}