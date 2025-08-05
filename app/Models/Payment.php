<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'booking_id',
        'payment_reference',
        'amount',
        'payment_date',
        'payment_method',
        'transaction_id',
        'status',
        'gateway_response',
        'refund_amount',
        'refund_date',
        'refund_reason'
    ];
    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'refund_date' => 'datetime',
    ];
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'id');
    }
}