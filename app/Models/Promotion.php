<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $table = 'promotions';


    protected $fillable = [
        'promotion_code',
        'description',
        'discount_type',
        'discount_value',
        'minimum_purchase',
        'start_date',
        'end_date',
        'usage_limit',
        'current_usage',
        'applicable_type',
        'is_active',
        'created_by',
        'created_at',
    ];

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function getDiscountTypeNameAttribute()
    {
        return $this->discount_type === 1 ? 'percentage' : 'fixed';
    }

    public function getIsValidAttribute()
    {
        $now = now();
        return $this->isActive &&
            $this->start_date <= $now &&
            $this->end_date >= $now &&
            (is_null($this->usage_limit) || $this->current_usage < $this->usage_limit);
    }
}
