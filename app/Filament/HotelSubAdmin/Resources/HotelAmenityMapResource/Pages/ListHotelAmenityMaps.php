<?php

namespace App\Filament\HotelSubAdmin\Resources\HotelAmenityMapResource\Pages;

use App\Filament\HotelSubAdmin\Resources\HotelAmenityMapResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHotelAmenityMaps extends ListRecords
{
    protected static string $resource = HotelAmenityMapResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
