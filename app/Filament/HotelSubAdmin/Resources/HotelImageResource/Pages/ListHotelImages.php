<?php

namespace App\Filament\HotelSubAdmin\Resources\HotelImageResource\Pages;

use App\Filament\HotelSubAdmin\Resources\HotelImageResource;
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
