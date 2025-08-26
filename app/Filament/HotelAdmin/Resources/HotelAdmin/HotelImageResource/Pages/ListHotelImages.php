<?php

namespace App\Filament\HotelAdmin\Resources\HotelAdmin\HotelImageResource\Pages;

use App\Filament\HotelAdmin\Resources\HotelAdmin\HotelImageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHotelImages extends ListRecords
{
    protected static string $resource = HotelImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
