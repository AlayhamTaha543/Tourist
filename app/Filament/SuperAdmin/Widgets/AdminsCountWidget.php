<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Admin;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminsCountWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Admins', Admin::count()),
        ];
    }
}
