<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_type_id',
        'date',
        'available_rooms',
        'price',
        'is_blocked'
    ];

    protected $casts = [
        'date' => 'date',
        'price' => 'decimal:2',
        'is_blocked' => 'boolean',
    ];

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class, 'room_type_id', 'id');
    }
}