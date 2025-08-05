<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentNotification extends Notification
{
    use Queueable;

    public $amount;
    public $paymentMethod;
    public $paymentReference;

    public function __construct($amount, $paymentMethod, $paymentReference)
    {
        $this->amount = $amount;
        $this->paymentMethod = $paymentMethod;
        $this->paymentReference = $paymentReference;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
        ->subject('Payment Successful')
        ->greeting("Hello {$notifiable->name},")
        ->line("An amount of **{$this->amount} SAR** has been deducted from your card using **{$this->paymentMethod}**.")
        ->line("Your payment reference number is: **{$this->paymentReference}**")
        ->line('Thank you for using our service.');
    }

   
}
