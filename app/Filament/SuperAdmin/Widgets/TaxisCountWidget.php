<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\TaxiService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TaxisCountWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Taxis', TaxiService::count()),
        ];
    }
}
