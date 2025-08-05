<?php

namespace App\Filament\TourAdmin\Widgets;

use App\Models\Tour;
use App\Models\TourBooking;
use Auth;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TourBookingPanelChart extends ChartWidget
{
    protected static ?string $heading = 'This Week Tour Bookings';
    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user
            && (($user->role === 'admin' && $user->section === 'tour') || ($user->role === 'sub_admin' && $user->section === 'tour'));
    }
    protected function getData(): array
    {
        $user = Auth::user();
        $baseQuery = TourBooking::query();

        if ($user->role === 'sub_admin' && $user->section === 'tour') {
            $tours = Tour::where('admin_id', $user->id)->pluck('id');

            if ($tours->isEmpty()) {
                return [
                    'datasets' => [],
                    'labels' => [],
                ];
            }

            $baseQuery->whereIn('tour_id', $tours);
        }

        $data = Trend::query($baseQuery)
            ->between(
                start: now()->startOfWeek(),
                end: now()->endOfWeek(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Weekly Tour Bookings',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => \Carbon\Carbon::parse($value->date)->translatedFormat('D')),
        ];
    }



    protected function getType(): string
    {
        return 'line';
    }
}
