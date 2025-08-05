<?php

namespace App\Filament\HotelAdmin\Resources\HotelBookingResource\Pages;

use App\Filament\HotelAdmin\Resources\HotelBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHotelBooking extends ViewRecord
{
    protected static string $resource = HotelBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
