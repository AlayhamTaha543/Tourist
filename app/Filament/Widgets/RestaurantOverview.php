<?php

namespace App\Filament\Widgets;

use App\Models\Admin;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Models\RestaurantBooking;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RestaurantOverview extends BaseWidget
{
    public static function canView(): bool
        {
            $user = Filament::auth()->user();
        
            return $user
                && ( ($user->role === 'admin' && $user->section === 'restaurant') || ($user->role === 'sub_admin' && $user->section === 'restaurant'));
        }
    protected function getStats(): array
    {
        $user = Filament::auth()->user();

        $adminCount = Admin::where('section', 'restaurant')->count();
        $restaurantCount = Restaurant::count();
        $categoryCount = MenuCategory::count();
        $itemCount = MenuItem::count();
        $bookingCount = RestaurantBooking::count();

        if ($user->role === 'admin' && $user->section === 'restaurant') {
            $restaurant = Restaurant::where('admin_id', $user->id)->first();

            if ($restaurant) {
                $adminCount = 1;
                $restaurantCount = 1;
                $categoryCount = MenuCategory::where('restaurant_id', $restaurant->id)->count();
                $itemCount = MenuItem::where('restaurant_id', $restaurant->id)->count();
                $bookingCount = RestaurantBooking::where('restaurant_id', $restaurant->id)->count();
            } else {
                $categoryCount = $itemCount = $bookingCount = 0;
            }
        }

        return [
            Stat::make('Admins', $adminCount)
                ->description('Admins in restaurant section')
                ->color('success'),

            Stat::make('Restaurants', $restaurantCount)
                ->description('Total Restaurants')
                ->color('primary'),

            Stat::make('Categories', $categoryCount)
                ->description('Total Menu Categories')
                ->color('info'),

            Stat::make('Items', $itemCount)
                ->description('Total Menu Items')
                ->color('warning'),

            Stat::make('Bookings', $bookingCount)
                ->description('Total Restaurant Bookings')
                ->color('danger'),
        ];
    }
}
