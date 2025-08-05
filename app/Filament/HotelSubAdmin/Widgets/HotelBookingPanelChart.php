<?php

namespace App\Filament\HotelSubAdmin\Widgets;

use App\Models\Hotel;
use App\Models\HotelBooking;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class HotelBookingPanelChart extends ChartWidget
{
    protected static ?string $heading = 'Hotel Booking';
    // protected int | string | array $columnSpan = 'full';
    public static function canView(): bool
        {
            $user = Filament::auth()->user();
        
            return $user
                && ($user->role === 'super_admin' || ($user->role === 'admin' && $user->section === 'hotel') || ($user->role === 'sub_admin' && $user->section === 'hotel'));
        }
    protected function getData(): array
    {
        $user = auth()->user();
    
        if ($user->role === 'admin') {
            $data = Trend::model(HotelBooking::class)
                ->between(
                    start: now()->startOfWeek(),
                    end: now()->endOfWeek(),
                )
                ->perDay()
                ->count();
        } else {
            $hotel = Hotel::where('admin_id', auth()->id())->first();
            
            if (!$hotel) {
                return [
                    'datasets' => [],
                    'labels' => [],
                ];
            }
            
            $data = Trend::query(
                HotelBooking::query()->where('hotel_id', $hotel->id)
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
