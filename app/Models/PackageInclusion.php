<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageInclusion extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'inclusion_type',
        'description',
        'is_highlighted'
    ];

    protected $casts = [
        'inclusion_type' => 'integer',
        'is_highlighted' => 'boolean',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(TravelPackage::class, 'package_id', 'id');
    }
}