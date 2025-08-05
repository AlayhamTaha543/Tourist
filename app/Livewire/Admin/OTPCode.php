<?php

namespace App\Livewire\Admin;

use App\Notifications\OTPNotification;
use Auth;
use Filament\Pages\Dashboard;
use Livewire\Component;

class OTPCode extends Component
{
    public $code;

    protected $rules = [
        'code' => 'required|digits:4',
    ];

    public function submit()
    {
        $this->validate();

        $user = auth('admin')->user();

        if ($user && $this->code == $user->code && $user->isCodeValid()) {
            $user->resetCode(); 
            $user->update(['email_verified_at' => now()]);

            session()->flash('success', 'Successfuly');
            return redirect()->to(Dashboard::getUrl(panel: 'admin'));
        }

        session()->flash('error', 'invalide');
    }
    

    public function render()
    {
        return view('.admin.o-t-p-code');
    }
}
