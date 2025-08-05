<?php

namespace App\Filament\TaxiAdmin\Resources\VehicleStatusChartResource\Widgets;

use Filament\Widgets\ChartWidget;

class VehicleStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
