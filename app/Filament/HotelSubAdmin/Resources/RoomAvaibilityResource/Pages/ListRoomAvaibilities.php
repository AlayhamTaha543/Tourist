<?php

namespace App\Filament\HotelSubAdmin\Resources\RoomAvaibilityResource\Pages;

use App\Filament\HotelSubAdmin\Resources\RoomAvaibilityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRoomAvaibilities extends ListRecords
{
    protected static string $resource = RoomAvaibilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
