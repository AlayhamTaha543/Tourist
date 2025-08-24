<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\RentalOffice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RentOfficesCountWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Rent Offices', RentalOffice::count()),
        ];
    }
}
