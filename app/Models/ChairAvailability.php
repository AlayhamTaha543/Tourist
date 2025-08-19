<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChairAvailability extends Model
{
    use HasFactory;

    protected $table = 'chair_availabilities'; // Explicitly set table name

    protected $fillable = [
        'restaurant_chair_id',
        'date',
        'time_slot',
        'available_chairs_count',
    ];

    protected $casts = [
        'date' => 'date',
        'time_slot' => 'datetime',
    ];

    public function restaurantChair(): BelongsTo
    {
        return $this->belongsTo(RestaurantChair::class, 'restaurant_chair_id', 'id');
    }
}
