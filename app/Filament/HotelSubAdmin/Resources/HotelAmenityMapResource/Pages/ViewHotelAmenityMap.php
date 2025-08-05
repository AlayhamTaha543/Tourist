<?php

namespace App\Filament\HotelSubAdmin\Resources\HotelAmenityMapResource\Pages;

use App\Filament\HotelSubAdmin\Resources\HotelAmenityMapResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewHotelAmenityMap extends ViewRecord
{
    protected static string $resource = HotelAmenityMapResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
