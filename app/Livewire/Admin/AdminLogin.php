<?php

namespace App\Livewire\Admin;

use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class AdminLogin extends Component
{
    public $email;
    public $password;
    public $remember;

    public function rules(){
        return[
            'email'=>'required|email|string',
            'password'=>'required',
            'remember'=>'nullable'
        ];
    }
    public function submit(){
        $this->validate();
        if(! Filament::auth()->attempt([
            'email'=>$this->email,
            'password'=>$this->password,
        ],$this->remember)){
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }
        Filament::auth()->login(Filament::auth()->user());
        session()->regenerate();
        if (Filament::auth()->user()->email_verified_at ==null) {
            return redirect()->route('otpCode');
        }
        return redirect()->to(Dashboard::getUrl(panel: 'admin'));
    }
    
    public function render()
    {
        return view('admin.admin-login');
    }
}
