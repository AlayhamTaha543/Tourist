<?php

namespace App\Filament\RestaurantAdmin\Resources\RestaurantBookingResource\Pages;

use App\Filament\RestaurantAdmin\Resources\RestaurantBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRestaurantBooking extends ViewRecord
{
    protected static string $resource = RestaurantBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
