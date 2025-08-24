<?php

namespace App\Filament\RentSubAdmin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.rent-sub-admin.pages.dashboard';

    protected static ?string $title = 'Rent Sub Admin Dashboard';
}
