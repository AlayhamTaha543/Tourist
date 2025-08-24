<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\TravelFlight;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FlightsCountWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Flights', TravelFlight::count()),
        ];
    }
}
