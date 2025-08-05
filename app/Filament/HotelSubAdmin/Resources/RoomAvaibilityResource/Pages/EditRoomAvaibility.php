<?php

namespace App\Filament\HotelSubAdmin\Resources\RoomAvaibilityResource\Pages;

use App\Filament\HotelSubAdmin\Resources\RoomAvaibilityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRoomAvaibility extends EditRecord
{
    protected static string $resource = RoomAvaibilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
