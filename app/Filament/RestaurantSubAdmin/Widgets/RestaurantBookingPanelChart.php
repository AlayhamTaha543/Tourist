<?php

namespace App\Filament\RestaurantSubAdmin\Widgets;

use App\Models\Restaurant;
use App\Models\RestaurantBooking;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class RestaurantBookingPanelChart extends ChartWidget
{
    protected static ?string $heading = 'ٌRestaurant Booking';
    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user
            && (($user->role === 'admin' && $user->section === 'restaurant') || ($user->role === 'sub_admin' && $user->section === 'restaurant'));
    }
    protected function getData(): array
    {
        $user = auth()->user(); 

        if ($user->role === 'admin') {
            $data = Trend::model(RestaurantBooking::class)
                ->between(
                    start: now()->startOfWeek(),
                    end: now()->endOfWeek(),
                )
                ->perDay()
                ->count();
        } else {
            $restaurant = Restaurant::where('admin_id', auth()->id())->first();

            if (!$restaurant) {
                return [
                    'datasets' => [],
                    'labels' => [],
                ];
            }

            $data = Trend::query(
                RestaurantBooking::query()->where('restaurant_id', $restaurant->id)
            )
            ->between(
                start: now()->startOfWeek(),
                end: now()->endOfWeek(),
            )
            ->perDay()
            ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'This Week Booking',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => \Carbon\Carbon::parse($value->date)->translatedFormat('D')),
        ];
    }


    protected function getType(): string
    {
        return 'line';
    }
}
