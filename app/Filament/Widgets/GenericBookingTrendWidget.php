<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Model;

class GenericBookingTrendWidget extends ChartWidget
{
    public ?string $modelClass = null;
    public ?string $label = null;

    protected static ?string $heading = null;

    public function getHeading(): string
    {
        return $this->label ?? 'Booking Trend';
    }

    protected function getData(): array
    {
        if (!$this->modelClass || !class_exists($this->modelClass) || !is_subclass_of($this->modelClass, Model::class)) {
            return [
                'datasets' => [
                    [
                        'label' => $this->label ?? 'Bookings',
                        'data' => [],
                    ],
                ],
                'labels' => [],
            ];
        }

        $data = Trend::model($this->modelClass)
            ->between(
                start: now()->subDays(7),
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => $this->label ?? 'Bookings',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#FF9F40', // Example color
                    'backgroundColor' => '#FF9F40', // Example color
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}