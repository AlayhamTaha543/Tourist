<?php

namespace App\Filament\TravelAdmin\Widgets;

use App\Models\Booking;
use App\Models\TravelAgency;
use App\Models\TravelBooking;
use App\Models\TravelPackage;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class TravelPanelChart extends ChartWidget
{
    protected static ?string $heading = 'This Week Travel Packages';
    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user
            && (($user->role === 'admin' && $user->section === 'travel') || ($user->role === 'sub_admin' && $user->section === 'travel'));
    }
    protected function getData(): array
    {
        $user = Auth::user();
        $baseQuery = TravelPackage::query(); 

        if ($user->role === 'sub_admin' && $user->section === 'travel') {
            $agency = TravelAgency::where('admin_id', $user->id)->first();

            if (!$agency) {
                return [
                    'datasets' => [],
                    'labels' => [],
                ];
            }

            $packageIds = $agency->packages()->pluck('id');
            $baseQuery->whereIn('id', $packageIds); 
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
                    'label' => 'Weekly Travel Packages',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate), // إجمالي الباقات لكل يوم
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

