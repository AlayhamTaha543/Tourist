<?php

namespace App\Filament\HotelAdmin\Resources\HotelBookingResource\Pages;

use App\Filament\HotelAdmin\Resources\HotelBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHotelBooking extends EditRecord
{
    protected static string $resource = HotelBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
