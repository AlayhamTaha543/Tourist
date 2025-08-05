<?php

namespace App\Filament\Widgets;

use App\Models\Admin;
use App\Models\TravelAgency;
use App\Models\PackageBooking;
use App\Models\Destination;
use App\Models\Booking;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TravelOverview extends BaseWidget
{
    public static function canView(): bool
    {
        $user = Filament::auth()->user();
    
        return $user
            && ( ($user->role === 'admin' && $user->section === 'travel') || ($user->role === 'sub_admin' && $user->section === 'travel'));
    }
    protected function getStats(): array
    {
        $user = Filament::auth()->user();

        $adminCount = Admin::where('section', 'travel')->count();
        $agencyCount = TravelAgency::count();
        $packageCount = PackageBooking::count();
        $bookingCount = PackageBooking::count();

        if ($user->role === 'sub_admin' && $user->section === 'travel') {
            $adminCount = 1;

            $agency = TravelAgency::where('admin_id', $user->id)->first();

            if ($agency) {
                $agencyCount = 1;
                $packageCount = PackageBooking::where('agency_id', $agency->id)->count();
                $bookingCount = PackageBooking::whereIn('id', function ($query) use ($agency) {
                    $query->select('id')
                        ->from('bookings')
                        ->whereIn('id', function ($q) use ($agency) {
                            $q->select('id')
                              ->from('travel_packages')
                              ->where('agency_id', $agency->id);
                        });
                })->count();
            } else {
                $agencyCount = $packageCount =  $bookingCount = 0;
            }
        }

        return [
            Stat::make('Admins', $adminCount)
                ->description('Travel Section Admins')
                ->color('success'),

            Stat::make('Agencies', $agencyCount)
                ->description('Total Travel Agencies')
                ->color('primary'),

            Stat::make('Packages', $packageCount)
                ->description('Total Travel Packages')
                ->color('info'),

            Stat::make('Bookings', $bookingCount)
                ->description('Total Travel Package Bookings')
                ->color('danger'),
        ];
    }
}

