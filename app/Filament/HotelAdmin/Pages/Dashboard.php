<?php

namespace App\Filament\HotelAdmin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.hotel-admin.pages.dashboard';

    protected static ?string $title = 'Hotel Admin Dashboard';
}
