<?php

namespace App\Filament\Widgets;

use App\Models\Hotel;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class HotelPanelChart extends ChartWidget
{
    protected static ?string $heading = 'Hotel';

    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user
            && ($user->role === 'super_admin' || ($user->role === 'admin' && $user->section === 'hotel') || ($user->role === 'sub_admin' && $user->section === 'hotel'));
    }
    protected function getData(): array
    {
        $data = Trend::model(Hotel::class)
            ->between(
                start: now()->startOfWeek(),
                end: now()->endOfWeek(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Hotels',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}