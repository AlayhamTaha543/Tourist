<?php

namespace App\Filament\HotelAdmin\Resources\HotelAdmin\RoomAvailabilityResource\Pages;

use App\Filament\HotelAdmin\Resources\HotelAdmin\RoomAvailabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoomAvailability extends EditRecord
{
    protected static string $resource = RoomAvailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
