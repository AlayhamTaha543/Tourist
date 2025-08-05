<?php

namespace App\Filament\TravelAdmin\Widgets;

use App\Models\PackageBooking;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use App\Models\TravelAgency;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\Auth;
class TravelBookingPanelChart extends ChartWidget
{
    protected static ?string $heading = 'This Week Travel Package Bookings';
    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user
            && (($user->role === 'admin' && $user->section === 'travel') || ($user->role === 'sub_admin' && $user->section === 'travel'));
    }
    protected function getData(): array
    {
        $user = Auth::user();
        $baseQuery = PackageBooking::query();

        if ($user->role === 'sub_admin' && $user->section === 'travel') {
            $agency = TravelAgency::where('admin_id', $user->id)->first();

            if (!$agency) {
                return [
                    'datasets' => [],
                    'labels' => [],
                ];
            }
            $packageIds = $agency->packages()->pluck('id');

            $baseQuery->whereIn('package_id', $packageIds);
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
                    'label' => 'Weekly Travel Package Bookings',
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
