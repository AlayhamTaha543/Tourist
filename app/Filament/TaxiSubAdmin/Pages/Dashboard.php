<?php

namespace App\Filament\TaxiSubAdmin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.taxi-sub-admin.pages.dashboard';

    protected static ?string $title = 'Taxi Sub Admin Dashboard';
}
