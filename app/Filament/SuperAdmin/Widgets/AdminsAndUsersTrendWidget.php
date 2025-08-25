<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Admin;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class AdminsAndUsersTrendWidget extends ChartWidget
{
    protected static ?string $heading = 'Admins and Users Trend';

    protected function getData(): array
    {
        $adminsData = Trend::model(Admin::class)
            ->between(
                start: now()->subDays(7),
                end: now(),
            )
            ->perDay()
            ->count();

        $usersData = Trend::model(User::class)
            ->between(
                start: now()->subDays(7),
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Admins',
                    'data' => $adminsData->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#FF6384',
                    'backgroundColor' => '#FF6384',
                ],
                [
                    'label' => 'Users',
                    'data' => $usersData->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#36A2EB',
                    'backgroundColor' => '#36A2EB',
                ],
            ],
            'labels' => $adminsData->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
