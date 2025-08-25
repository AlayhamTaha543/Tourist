<?php

namespace App\Filament\TaxiAdmin\Pages;

use App\Filament\Widgets\GenericBookingTrendWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.taxi-admin.pages.dashboard';

    protected static ?string $title = 'Taxi Admin Dashboard';

    public function getWidgets(): array
    {
        return [
            GenericBookingTrendWidget::make([
                'modelClass' => \App\Models\TaxiBooking::class,
                'label' => 'Taxi Bookings Trend',
            ]),
        ];
    }
}