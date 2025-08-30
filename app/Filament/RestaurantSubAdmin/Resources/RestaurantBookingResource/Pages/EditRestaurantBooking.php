<?php

namespace App\Filament\RestaurantSubAdmin\Resources\RestaurantBookingResource\Pages;

use App\Filament\RestaurantSubAdmin\Resources\RestaurantBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRestaurantBooking extends EditRecord
{
    protected static string $resource = RestaurantBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
