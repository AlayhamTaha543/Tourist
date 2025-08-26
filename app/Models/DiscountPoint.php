<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountPoint extends Model
{
    protected $fillable = ['action', 'min_points', 'required_points', 'discount_percentage'];
}