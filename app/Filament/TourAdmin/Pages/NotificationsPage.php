<?php

namespace App\Filament\TourAdmin\Pages;

use Filament\Pages\Page;

class NotificationsPage extends Page
{

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static string $view = 'filament.tour-admin.pages.notifications-page';
    protected static ?string $navigationLabel = 'Notification';
    protected static ?string $title = 'Notification';
    protected array $properties = [];
    public $notifications;
    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin' && auth()->user()->section === 'tour';
    }
    public static function getDefaultProperties(): array
{
    return [];
}
    public function mount(): void
    {
        $this->notifications = auth()->user()
            ->notifications()
            ->where('data->type', 'tour_admin_request')
            ->latest()
            ->get();
    }
}
