<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TourAdminRequestNotification extends Notification
{
    use Queueable;

    public $user;
    public $tour;

    public function __construct($user, $tour)
    { $this->user = $user;
        if (is_array($tour)) {
            $this->tour = (object) $tour;
        } else {
            $this->tour = $tour;
        }
    }


    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Tour Admin Request')
            ->greeting("Hello {$notifiable->name},")
            ->line("User {$this->user->name} ({$this->user->email}) is requesting to become a tour admin.")
            ->line("Tour Information:")
            ->line("• Name: {$this->tour->tour_name}")
            ->line("• Destination: {$this->tour->location_id}")
            ->line("• Description: {$this->tour->description}")
            ->line("• Short Description: {$this->tour->short_description}")
            ->line("• Duration (Hours): {$this->tour->duration_hours}")
            ->line("• Duration (Days): {$this->tour->duration_days}")
            ->line("• Base Price: {$this->tour->base_price}")
            ->line("• Discount: {$this->tour->discount_percentage}%")
            ->line("• Max Capacity: {$this->tour->max_capacity}")
            ->line("• Min Participants: {$this->tour->min_participants}")
            ->line("• Difficulty: {$this->tour->difficulty_level}")
            ->line("• Start Date: {$this->tour->start_date}")
            ->line("• End Date: {$this->tour->end_date}")
            ->line("Please review this request and take appropriate action.")
            ->line('Thank you.');
    }
    
    public function toArray($notifiable): array
    {
        return [
            'type' => 'tour_admin_request',
            'user_name' => $this->user->first_name,
            'user_email' => $this->user->email,
            'tour_name' => $this->tour->tour_name,
            'destination' => $this->tour->location_id,
            'description' => $this->tour->description,
            'short_description' => $this->tour->short_description,
            'duration_hours' => $this->tour->duration_hours,
            'duration_days' => $this->tour->duration_days,
            'base_price' => $this->tour->base_price,
            'discount_percentage' => $this->tour->discount_percentage,
            'max_capacity' => $this->tour->max_capacity,
            'min_participants' => $this->tour->min_participants,
            'difficulty_level' => $this->tour->difficulty_level,
            'start_date' => $this->tour->start_date,
            'end_date' => $this->tour->end_date,
            'message' => "{$this->user->name} is requesting to become a tour admin for the tour '{$this->tour->tour_name}'.",
        ];
    }

}
