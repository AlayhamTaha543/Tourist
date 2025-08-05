<?php

namespace App\Filament\HotelAdmin\Resources\HotelAmenityResource\Pages;

use App\Filament\HotelAdmin\Resources\HotelAmenityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHotelAmenities extends ListRecords
{
    protected static string $resource = HotelAmenityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
