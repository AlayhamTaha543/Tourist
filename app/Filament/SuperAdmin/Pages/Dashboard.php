<?php

namespace App\Filament\SuperAdmin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.super-admin.pages.dashboard';

    protected static ?string $title = 'Super Admin Dashboard';
}
