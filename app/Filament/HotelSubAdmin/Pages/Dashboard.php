<?php

namespace App\Filament\HotelSubAdmin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.hotel-sub-admin.pages.dashboard';

    protected static ?string $title = 'Hotel Sub Admin Dashboard';
}
