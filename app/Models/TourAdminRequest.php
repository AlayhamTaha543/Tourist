<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourAdminRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'age',
        'skills',
        'personal_image',
        'certificate_image',
        'status',
    ];

    protected $casts = [
        'skills' => 'array',
    ];
}