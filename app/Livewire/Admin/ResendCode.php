<?php

namespace App\Livewire\Admin;

use App\Notifications\OTPNotification;
use Auth;
use Livewire\Component;

class ResendCode extends Component
{
    public function submit()
    {
        $user = auth('admin')->user();

        if ($user && $user->email_verified_at == null) {
            $user->timestamps=false;
            $user->code=rand(1000,9999);
            $user->expire_at=now()->addMinutes(10);
            $user->save();
            $user->notify(new OTPNotification());

            session()->flash('success', 'A new verification code has been sent.');
        }
        
        session()->flash('error', 'An Error Happened.');
    }
    public function render()
    {
        return view('admin.resend-code');
    }
}
