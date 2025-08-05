<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Restaurant;
use App\Models\RestaurantBooking;
use App\Models\RestaurantTable;
use App\Models\User;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminPanelOverview extends BaseWidget
{
    public static function canView(): bool
    {
        $user = Filament::auth()->user();

        return $user
            && ($user->role === 'super_admin');
    }
    protected function getStats(): array
    {
        $user = Filament::auth()->user();

        $userCount = User::count();
        $restaurantCount = Restaurant::count();
        $bookingCount = RestaurantBooking::count();
        $tableCount = RestaurantTable::count();
        $userID=Booking::all();

        // if ($user->role === 'admin' && $user->section === 'restaurant') {
        //     $restaurantId = $userID->resta; 
        //     $bookingIds = RestaurantBooking::where('restaurant_id', $restaurantId)->pluck('user_id')->unique();
        //     $userCount = User::whereIn('id', $bookingIds)->count();
        //     $restaurantCount = 1;
        //     $bookingCount = RestaurantBooking::where('restaurant_id', $restaurantId)->count();
        //     $tableCount = RestaurantTable::where('restaurant_id', $restaurantId)->count();
        // }
            return [
                Stat::make('Users', $userCount)
                    ->description('Last 7 days')
                    ->color('success'),
            
                Stat::make('Cities', $restaurantCount)
                    ->description('Total Cities')
                    ->color('danger'),
            
                Stat::make('Employee Bookings', $bookingCount)
                    ->description('Total Employees')
                    ->color('primary'),
            
                Stat::make('Employee Tables', $tableCount)
                    ->description('Total Employees')
                    ->color('primary'),
            ];
    }
}
