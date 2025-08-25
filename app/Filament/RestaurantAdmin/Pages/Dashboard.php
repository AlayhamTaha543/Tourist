<?php

namespace App\Filament\RestaurantAdmin\Pages;

use App\Filament\Widgets\GenericBookingTrendWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.restaurant-admin.pages.dashboard';

    protected static ?string $title = 'Restaurant Admin Dashboard';

    public function getWidgets(): array
    {
        return [
            GenericBookingTrendWidget::make([
                'modelClass' => \App\Models\RestaurantBooking::class,
                'label' => 'Restaurant Bookings Trend',
            ]),
        ];
    }
}