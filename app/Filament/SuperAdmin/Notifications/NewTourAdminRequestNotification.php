<?php

namespace App\Filament\SuperAdmin\Notifications;

use App\Filament\SuperAdmin\Resources\TourAdminRequestResource;
use App\Models\TourAdminRequest;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification as BaseNotification;

class NewTourAdminRequestNotification extends BaseNotification implements ShouldQueue
{
    use Queueable;

    protected TourAdminRequest $tourAdminRequest;

    public function __construct(TourAdminRequest $tourAdminRequest)
    {
        $this->tourAdminRequest = $tourAdminRequest;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'filament'];
    }

    public function toFilament(object $notifiable): Notification
    {
        return Notification::make()
            ->title('New Tour Admin Request')
            ->body("A new tour admin request from {$this->tourAdminRequest->full_name} is awaiting your review.")
            ->icon('heroicon-o-user-plus')
            ->color('info')
            ->actions([
                Action::make('view')
                    ->label('View Request')
                    ->url(fn(): string => TourAdminRequestResource::getUrl('view', ['record' => $this->tourAdminRequest->id]))
                    ->button(),
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tour_admin_request_id' => $this->tourAdminRequest->id,
            'full_name' => $this->tourAdminRequest->full_name,
            'email' => $this->tourAdminRequest->email,
        ];
    }
}