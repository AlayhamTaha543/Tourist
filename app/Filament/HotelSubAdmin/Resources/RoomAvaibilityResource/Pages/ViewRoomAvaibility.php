<?php

namespace App\Filament\HotelSubAdmin\Resources\RoomAvaibilityResource\Pages;

use App\Filament\HotelSubAdmin\Resources\RoomAvaibilityResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRoomAvaibility extends ViewRecord
{
    protected static string $resource = RoomAvaibilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
