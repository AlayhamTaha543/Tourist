<?php

namespace App\Filament\TravelAdmin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.travel-admin.pages.dashboard';

    protected static ?string $title = 'Travel Admin Dashboard';
}
