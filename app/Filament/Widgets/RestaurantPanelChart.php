<?php

namespace App\Filament\Widgets;

use App\Models\Restaurant;
use App\Models\RestaurantBooking;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class RestaurantPanelChart extends ChartWidget
{
    protected static ?string $heading = 'Restaurant';
    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user
            && ($user->role === 'super_admin' || ($user->role === 'admin' && $user->section === 'restaurant') || ($user->role === 'sub_admin' && $user->section === 'restaurant'));
    }
    protected function getData(): array
    {
        $data = Trend::model(Restaurant::class)
        ->between(
            start: now()->startOfWeek(),
            end: now()->endOfWeek(),
        )
        ->perDay()
        ->count();

    return [
        'datasets' => [
            [
                'label' => 'Restaurants',
                'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
            ],
        ],
        'labels' => $data->map(fn (TrendValue $value) => $value->date),
    ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
