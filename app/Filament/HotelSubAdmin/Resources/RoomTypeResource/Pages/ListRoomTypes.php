<?php

namespace App\Filament\HotelSubAdmin\Resources\RoomTypeResource\Pages;

use App\Filament\HotelSubAdmin\Resources\RoomTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoomTypes extends ListRecords
{
    protected static string $resource = RoomTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
