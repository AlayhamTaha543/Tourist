<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class PaymentsAmountTrendWidget extends ChartWidget
{
    protected static ?string $heading = 'Payments Amount Trend';

    protected function getData(): array
    {
        $paymentsData = Trend::model(Payment::class)
            ->between(
                start: now()->subDays(7),
                end: now(),
            )
            ->perDay()
            ->sum('amount');

        return [
            'datasets' => [
                [
                    'label' => 'Total Payments',
                    'data' => $paymentsData->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#4BC0C0',
                    'backgroundColor' => '#4BC0C0',
                ],
            ],
            'labels' => $paymentsData->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}