<?php

namespace App\Filament\RestaurantAdmin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.restaurant-admin.pages.dashboard';

    protected static ?string $title = 'Restaurant Admin Dashboard';
}
