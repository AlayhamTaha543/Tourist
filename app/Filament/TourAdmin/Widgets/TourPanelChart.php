<?php

namespace App\Filament\TourAdmin\Widgets;

use App\Models\Tour;
use App\Models\TourBooking;
use Auth;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TourPanelChart extends ChartWidget
{
    protected static ?string $heading = 'This Week Tours';
    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user
            && (($user->role === 'admin' && $user->section === 'tour') || ($user->role === 'sub_admin' && $user->section === 'tour'));
    }
    protected function getData(): array
    {
        $user = Auth::user();
        $baseQuery = Tour::query(); 

        if ($user->role === 'sub_admin' && $user->section === 'travel') {
            $agency = Tour::where('admin_id', $user->id)->first();

            if (!$agency) {
                return [
                    'datasets' => [],
                    'labels' => [],
                ];
            }

            $tourIds = $agency->tours()->pluck('id');
            $baseQuery->whereIn('id', $tourIds); 
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
                    'label' => 'Weekly Tours',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate), // إجمالي الرحلات لكل يوم
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => \Carbon\Carbon::parse($value->date)->translatedFormat('D')), // تنسيق التاريخ ليظهر الأيام
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
