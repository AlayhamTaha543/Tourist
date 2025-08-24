<?php

namespace App\Filament\RestaurantSubAdmin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.restaurant-sub-admin.pages.dashboard';

    protected static ?string $title = 'Restaurant Sub Admin Dashboard';
}
