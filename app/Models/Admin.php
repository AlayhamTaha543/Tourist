<?php

namespace App\Models;
use App\Notifications\OTPNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard='admin';
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'section',
        'code',
        'expire_at',
        'email_verified_at'
    ];
    protected static function booted()
    {
        static::created(function ($admin) {
            $admin->code = rand(1000, 9999);
            $admin->expire_at = now()->addMinutes(6);
            $admin->save();
    
            $admin->notify(new OTPNotification());
        });
    }
    public function restaurant()
{
    return $this->hasOne(Restaurant::class, 'admin_id');
}
    public function hotel()
    {
        return $this->hasOne(Hotel::class, 'admin_id');
    }
    public function travel()
    {
        return $this->hasOne(TravelAgency::class, 'admin_id');
    }

public function generateCode(){

    $this->timestamps=false;
    $this->code=rand(1000,9999);
    $this->expire_at=now()->addMinutes(10);
    $this->save();
}
public function isCodeValid() {
    return $this->expire_at > now();
}

public function resetCode(){
    $this->timestamps=false;
    $this->code=null;
    $this->expire_at=null;
    $this->save();
}
}
