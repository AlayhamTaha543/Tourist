<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TourCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'parent_category_id',
        'icon',
        'display_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(TourCategory::class, 'parentCategory_id', 'id');
    }

    public function childCategories(): HasMany
    {
        return $this->hasMany(TourCategory::class, 'parentCategory_id', 'id');
    }

    public function tours()
    {
        return $this->belongsToMany(
            Tour::class,
            'TourCategoryMapping',
            'category_id',
            'tour_id'
        );
    }
}