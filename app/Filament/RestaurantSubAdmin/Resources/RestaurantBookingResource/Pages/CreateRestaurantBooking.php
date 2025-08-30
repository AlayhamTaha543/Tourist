<?php

namespace App\Filament\RestaurantSubAdmin\Resources\RestaurantBookingResource\Pages;

use App\Filament\RestaurantSubAdmin\Resources\RestaurantBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRestaurantBooking extends CreateRecord
{
    protected static string $resource = RestaurantBookingResource::class;
}
