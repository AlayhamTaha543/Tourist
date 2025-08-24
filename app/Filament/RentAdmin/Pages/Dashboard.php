<?php

namespace App\Filament\RentAdmin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.rent-admin.pages.dashboard';

    protected static ?string $title = 'Rent Admin Dashboard';
}
