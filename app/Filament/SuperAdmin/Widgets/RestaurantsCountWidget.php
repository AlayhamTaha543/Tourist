<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Restaurant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RestaurantsCountWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Restaurants', Restaurant::count()),
        ];
    }
}
