<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TourAdminRequestAcceptedNotification extends Notification
{
    use Queueable;

    public $tourName;

    public function __construct($tourName)
    {
        $this->tourName = $tourName;
    }

    public function via($notifiable)
    {
        return ['mail', 'database']; 
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your request accepted  ')
            ->greeting("Hello {$notifiable->name},")
            ->line(" your request was accepted to create tour: {$this->tourName}.")
            ->line('you can contact with us for more question')
            ->line('Thanks for using our application  .');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "your request was Accepted to create tour: {$this->tourName}.",
            'tour_name' => $this->tourName,
            'type' => 'tour_admin_request_accepted',
        ];
    }
}