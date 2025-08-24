<?php

namespace App\Filament\TourSubAdmin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.tour-sub-admin.pages.dashboard';

    protected static ?string $title = 'Tour Sub Admin Dashboard';
}
