<?php

namespace App\Filament\TourAdmin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.tour-admin.pages.dashboard';

    protected static ?string $title = 'Tour Admin Dashboard';
}
