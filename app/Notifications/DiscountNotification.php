<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DiscountNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public string $entityName;
    public float $discount;
    public string $entityType;
    public function __construct($entityName, $discount, $entityType)
    {
        $this->entityName = $entityName;
        $this->discount = $discount;
        $this->entityType = $entityType;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Discount Added {$this->discount}% in {$this->entityType} {$this->entityName}.",
        ];
    }
}
