<?php

namespace App\Filament\TravelSubAdmin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.travel-sub-admin.pages.dashboard';

    protected static ?string $title = 'Travel Sub Admin Dashboard';
}
