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
        'rating',
        'location_id',
        'manager_id',
        'image'
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
    public function vehicleCategories()
    {
        return $this->hasManyThrough(
            RentalVehicleCategory::class,
            RentalVehicle::class,
            'office_id', // Foreign key on RentalVehicle table
            'id', // Foreign key on RentalVehicleCategory table
            'id', // Local key on RentalOffice table
            'category_id' // Local key on RentalVehicle table
        )->distinct();
    }

    public function feedbacks()
    {
        return $this->morphMany(FeedBack::class, 'feedbackable');
    }
}
