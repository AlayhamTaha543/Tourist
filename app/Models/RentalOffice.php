<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RentalOffice extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'location_id',
        'manager_id',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function manager()
    {
        return $this->belongsTo(Admin::class, 'manager_id');
    }

    public function vehicles()
    {
        return $this->hasMany(RentalVehicle::class, 'office_id');
    }
}
