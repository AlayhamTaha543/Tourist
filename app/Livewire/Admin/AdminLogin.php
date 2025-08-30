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

    public function rules()
    {
        return [
            'email' => 'required|email|string',
            'password' => 'required',
            'remember' => 'nullable'
        ];
    }
    public function submit()
    {
        $this->validate();
        if (
            !Filament::auth()->attempt([
                'email' => $this->email,
                'password' => $this->password,
            ], $this->remember)
        ) {
            $this->addError('form', trans('auth.failed'));
            return;
        }
        Filament::auth()->login(Filament::auth()->user());
        session()->regenerate();

        $admin = Filament::auth()->user();

        if ($admin->role === 'super_admin') {
            return redirect()->to(Dashboard::getUrl(panel: 'admin'));
        }

        if ($admin->role === 'admin') {
            switch ($admin->section) {
                case 'restaurant':
                    return redirect()->to(Dashboard::getUrl(panel: 'restaurantAdmin'));
                case 'hotel':
                    return redirect()->to(Dashboard::getUrl(panel: 'hotelAdmin'));
                case 'tour':
                    return redirect()->to(Dashboard::getUrl(panel: 'tourAdmin'));
                case 'taxi':
                    return redirect()->to(Dashboard::getUrl(panel: 'taxiAdmin'));
                case 'rental':
                    return redirect()->to(Dashboard::getUrl(panel: 'rentAdmin'));
                case 'travel':
                    return redirect()->to(Dashboard::getUrl(panel: 'travelAdmin'));
                default:
                    return redirect()->to(Dashboard::getUrl(panel: 'admin'));
            }
        }

        if ($admin->role === 'sub_admin') {
            switch ($admin->section) {
                case 'restaurant':
                    return redirect()->to(Dashboard::getUrl(panel: 'restaurantSubAdmin'));
                case 'hotel':
                    return redirect()->to(Dashboard::getUrl(panel: 'hotelSubAdmin'));
                case 'rental':
                    return redirect()->to(Dashboard::getUrl(panel: 'rentSubAdmin'));
                case 'travel':
                    return redirect()->to(Dashboard::getUrl(panel: 'travelSubAdmin'));
                case 'taxi':
                    return redirect()->to(Dashboard::getUrl(panel: 'taxiSubAdmin'));
                default:
                    return redirect()->to(Dashboard::getUrl(panel: 'admin'));
            }
        }
    }

    public function render()
    {
        return view('admin.admin-login');
    }
}