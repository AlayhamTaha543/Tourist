<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Hotel;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HotelsCountWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Hotels', Hotel::count()),
        ];
    }
}
