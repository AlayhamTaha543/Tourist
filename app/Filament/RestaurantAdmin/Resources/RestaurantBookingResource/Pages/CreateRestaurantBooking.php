<?php

namespace App\Filament\RestaurantAdmin\Resources\RestaurantBookingResource\Pages;

use App\Filament\RestaurantAdmin\Resources\RestaurantBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRestaurantBooking extends CreateRecord
{
    protected static string $resource = RestaurantBookingResource::class;
}
