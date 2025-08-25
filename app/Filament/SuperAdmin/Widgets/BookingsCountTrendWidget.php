<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class BookingsCountTrendWidget extends ChartWidget
{
    protected static ?string $heading = 'Bookings Trend';

    protected function getData(): array
    {
        $bookingsData = Trend::model(Booking::class)
            ->between(
                start: now()->subDays(7),
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Total Bookings',
                    'data' => $bookingsData->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#FF9F40',
                    'backgroundColor' => '#FF9F40',
                ],
            ],
            'labels' => $bookingsData->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
