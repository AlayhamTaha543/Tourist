<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourGuideSkill extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'skills',
    ];

    protected $casts = [
        'skills' => 'array',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
