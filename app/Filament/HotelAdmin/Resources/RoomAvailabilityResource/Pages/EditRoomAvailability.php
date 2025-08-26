<?php

namespace App\Filament\HotelAdmin\Resources\RoomAvailabilityResource\Pages;

use App\Filament\HotelAdmin\Resources\RoomAvailabilityResource;
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
