<?php

namespace App\Filament\TravelAdmin\Resources\TravelBookingResource\Pages;

use App\Filament\TravelAdmin\Resources\TravelBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTravelBookings extends ListRecords
{
    protected static string $resource = TravelBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
