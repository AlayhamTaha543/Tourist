<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TableAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_id',
        'date',
        'time_slot',
        'is_available',
        'is_blocked',
        'price_multiplier' // For peak hours pricing
    ];

    protected $casts = [
        'date' => 'date',
        'time_slot' => 'datetime',
        'is_available' => 'boolean',
        'is_blocked' => 'boolean',
        'price_multiplier' => 'decimal:2',
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id', 'id');
    }
}