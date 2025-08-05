<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'acticity_id',
        'is_active'
    ];



}
