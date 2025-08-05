<?php

namespace App\Filament\Widgets;

use App\Models\Tour;
use App\Models\TourBooking;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TourOverview extends BaseWidget
{
    public static function canView(): bool
    {
        $user = Filament::auth()->user();
    
        return $user
            && ( ($user->role === 'admin' && $user->section === 'tour') || ($user->role === 'sub_admin' && $user->section === 'tour'));
    }
    protected function getStats(): array
    {
        $user = Filament::auth()->user(); 

    $tourCount = Tour::count(); 
    $bookingCount = TourBooking::count(); 

    if ($user->role === 'sub_admin' && $user->section === 'tour') {
        $tourCount = 0;
        $bookingCount = 0;

        $tours = Tour::where('admin_id', $user->id)->get();

        if ($tours->isNotEmpty()) {
            $tourCount = $tours->count();
            $bookingCount = TourBooking::whereIn('tour_id', $tours->pluck('id'))->count();
        }
    }

    return [
        Stat::make('Tours', $tourCount)
            ->description('Total Tours')
            ->color('primary'),

        Stat::make('Bookings', $bookingCount)
            ->description('Total Tour Bookings')
            ->color('success'),
    ];
    }
}
