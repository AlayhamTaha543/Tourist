<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
     protected $fillable = [
        'service_type',
        'service_id',
        'policy_type',
        'cutoff_time',
        'penalty_percentage',
    ];

    public function service()
    {
        return $this->morphTo(); 
    }
}
