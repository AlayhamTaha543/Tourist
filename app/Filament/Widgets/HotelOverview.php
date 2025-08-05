<?php

namespace App\Filament\Widgets;

use App\Models\Admin;
use App\Models\Hotel;
use App\Models\HotelBooking;
use App\Models\RoomType;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HotelOverview extends BaseWidget
{
    public static function canView(): bool
        {
            $user = Filament::auth()->user();
        
            return $user
                && ( ($user->role === 'admin' && $user->section === 'hotel') || ($user->role === 'sub_admin' && $user->section === 'hotel'));
        }
    protected function getStats(): array
    {
        $user = Filament::auth()->user();

        $userCount = Admin::where('section','hotel')->count();
        $hotelCount = Hotel::count();
        $roomTypeCount = RoomType::count();
        $bookingCount = HotelBooking::count();

    if ($user->role === 'admin' && $user->section === 'hotel') {
        $hotel = Hotel::where('admin_id', $user->id)->first();

        if ($hotel) {
            $userCount = User::where('id', $user->id)->count(); 
            $hotelCount = 1; 
            $roomTypeCount = RoomType::where('hotel_id', $hotel->id)->count();
            $bookingCount = HotelBooking::where('hotel_id', $hotel->id)->count();
        }
    }

    return [
        Stat::make('Admins', $userCount)
            ->description('Admin Count')
            ->color('success'),

        Stat::make('Hotels', $hotelCount)
            ->description(' Hotel Count')
            ->color('primary'),

        Stat::make('Room Types', $roomTypeCount)
            ->description('Room Type Count  ')
            ->color('info'),

        Stat::make('Bookings', $bookingCount)
            ->description('Hotel Booking Type ')
            ->color('danger'),
    ];
    }
}
