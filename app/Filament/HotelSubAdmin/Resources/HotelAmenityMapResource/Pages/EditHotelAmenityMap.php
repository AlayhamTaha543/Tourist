<?php

namespace App\Filament\HotelSubAdmin\Resources\HotelAmenityMapResource\Pages;

use App\Filament\HotelSubAdmin\Resources\HotelAmenityMapResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHotelAmenityMap extends EditRecord
{
    protected static string $resource = HotelAmenityMapResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
