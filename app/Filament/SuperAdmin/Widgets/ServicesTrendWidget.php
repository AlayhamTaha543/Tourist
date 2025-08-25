<?php

namespace App\Filament\SuperAdmin\Widgets;

use App\Models\Hotel;
use App\Models\RentalOffice;
use App\Models\Restaurant;
use App\Models\TaxiService;
use App\Models\Tour;
use App\Models\TravelFlight;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class ServicesTrendWidget extends ChartWidget
{
    protected static ?string $heading = 'Services Trend';

    protected function getData(): array
    {
        $flightsData = Trend::model(TravelFlight::class)
            ->between(
                start: now()->subDays(7),
                end: now(),
            )
            ->perDay()
            ->count();

        $hotelsData = Trend::model(Hotel::class)
            ->between(
                start: now()->subDays(7),
                end: now(),
            )
            ->perDay()
            ->count();

        $rentOfficesData = Trend::model(RentalOffice::class)
            ->between(
                start: now()->subDays(7),
                end: now(),
            )
            ->perDay()
            ->count();

        $restaurantsData = Trend::model(Restaurant::class)
            ->between(
                start: now()->subDays(7),
                end: now(),
            )
            ->perDay()
            ->count();

        $taxisData = Trend::model(TaxiService::class)
            ->between(
                start: now()->subDays(7),
                end: now(),
            )
            ->perDay()
            ->count();

        $toursData = Trend::model(Tour::class)
            ->between(
                start: now()->subDays(7),
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Flights',
                    'data' => $flightsData->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#FF6384',
                    'backgroundColor' => '#FF6384',
                ],
                [
                    'label' => 'Hotels',
                    'data' => $hotelsData->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#36A2EB',
                    'backgroundColor' => '#36A2EB',
                ],
                [
                    'label' => 'Rent Offices',
                    'data' => $rentOfficesData->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#FFCE56',
                    'backgroundColor' => '#FFCE56',
                ],
                [
                    'label' => 'Restaurants',
                    'data' => $restaurantsData->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#4BC0C0',
                    'backgroundColor' => '#4BC0C0',
                ],
                [
                    'label' => 'Taxis',
                    'data' => $taxisData->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#9966FF',
                    'backgroundColor' => '#9966FF',
                ],
                [
                    'label' => 'Tours',
                    'data' => $toursData->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => '#FF9F40',
                    'backgroundColor' => '#FF9F40',
                ],
            ],
            'labels' => $flightsData->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}