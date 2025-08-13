<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;
    protected $fillable = [
        'image',
        'first_name',
        'last_name',
        'phone_number',
        'location',
        'password',
        'code',
        'expire_at',
        'email',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the country that owns the user.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'CountryID', 'CountryID');
    }

    public function rank()
    {
        return $this->belongsTo(UserRank::class, 'user_id', 'id');
    }
    public function createdTours(): HasMany
    {
        return $this->hasMany(Tour::class, 'created_by', 'id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'user_id', 'id');
    }
    public function favoriteTours()
    {
        return $this->morphedByMany(Tour::class, 'favoritable', 'favorites');
    }

    public function favoriteHotels()
    {
        return $this->morphedByMany(Hotel::class, 'favoritable', 'favorites');
    }

    public function generateCode()
    {

        $this->timestamps = false;
        $this->code = rand(1000, 9999);
        $this->expire_at = now()->addMinutes(10);
        $this->save();
    }
    public function isCodeValid()
    {
        return $this->expire_at > now();
    }

    public function resetCode()
    {
        $this->timestamps = false;
        $this->code = null;
        $this->expire_at = null;
        $this->save();
    }
    public function getFilamentName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name) ?: $this->email;
    }

    /**
     * Get the full name attribute.
     *
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name) ?: $this->email;
    }

    /**
     * Get the username for authentication (email field).
     *
     * @return string
     */
    public function username(): string
    {
        return $this->email;
    }
}