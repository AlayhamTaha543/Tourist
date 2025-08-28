<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Application extends Model
{
    use HasFactory;

    // Since this model represents the application itself and not a database table,
    // we can disable timestamps and mass assignment protection if no table is intended.
    public $timestamps = false;
    protected $guarded = [];

    // If you intend to store application-specific settings or data,
    // you might define a table and fillable properties here.
    // For now, it serves as a placeholder for polymorphic relationships.
}