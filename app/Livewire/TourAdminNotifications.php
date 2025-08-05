<?php

namespace App\Livewire;

use App\Models\Admin;
use App\Models\Tour;
use App\Models\User;
use App\Notifications\TourAdminRequestAcceptedNotification;
use App\Notifications\TourAdminRequestRejectedNotification;
use Hash;
use Livewire\Component;

class TourAdminNotifications extends Component
{
    public $notifications;

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = auth()->user()
            ->notifications()
            ->where('data->type', 'tour_admin_request')
            ->latest()
            ->get();
    }

    public function acceptRequest($id)
    {
            $notification = auth()->user()->notifications()->find($id);
        
            if (!$notification) {
                session()->flash('error', 'Notification not found');
                return;
            }
        
            $data = $notification->data;
            $userEmail = $data['user_email'] ?? null;

        
            $admin = Admin::create([
                'name' => $data['user_name'] ?? 'Unknown',
                'email' => $data['user_email'] ?? 'unknown@example.com',
                'password' => Hash::make('11111111'), 
                'role' => 'sub_admin', 
                'section' => 'tour',
            ]);
        
            $tour = Tour::create([
                'name' => $data['tour_name'],
                'short_description' => $data['short_description'],
                'description' => $data['description'],
                'location_id' => $data['location_id'],
                'duration_hours' => $data['duration_hours'],
                'duration_days' => $data['duration_days'],
                'base_price' => $data['base_price'],
                'discount_percentage' => $data['discount_percentage'],
                'max_capacity' => $data['max_capacity'],
                'min_participants' => $data['min_participants'],
                'difficulty_level' => $data['difficulty_level'],
                'main_image' => $data['main_image'],
                'admin_id' => $admin->id,
            ]);
            if ($userEmail) {
                $requestUser = User::where('email', $userEmail)->first();
                if ($requestUser) {
                    $requestUser->notify(new TourAdminRequestAcceptedNotification($tour));
                }
            }
            $notification->delete();
        
            $this->loadNotifications();
        
            session()->flash('success', 'Request accepted successfully. Admin and Tour created.');
        }
    

    public function rejectRequest($id)
    {
        $notification = auth()->user()->notifications()->find($id);

        if (!$notification) {
            session()->flash('error', 'Notification not found');
            return;
        }
    
        $userEmail = $notification->data['user_email'] ?? null;
    
        if ($userEmail) {
            $requestUser = User::where('email', $userEmail)->first();
    
            if ($requestUser) {
                $requestUser->notify(new TourAdminRequestRejectedNotification($notification->data['tour_name']));
            }
        }

    
        $notification->delete();
    
        $this->loadNotifications();
    
        session()->flash('success', 'Request rejected successfully and user notified.');
    }

    public function render()
    {
        return view('tour-admin-notifications');
    }
}
