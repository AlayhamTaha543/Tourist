<?php

namespace App\Filament\HotelSubAdmin\Resources\HotelBookingResource\Pages;

use App\Filament\HotelSubAdmin\Resources\HotelBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHotelBookings extends ListRecords
{
    protected static string $resource = HotelBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
