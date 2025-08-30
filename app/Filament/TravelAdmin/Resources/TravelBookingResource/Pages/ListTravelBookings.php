<?php

namespace App\Filament\TravelAdmin\Resources\TravelAdmin\TravelBookingResource\Pages;

use App\Filament\TravelAdmin\Resources\TravelAdmin\TravelBookingResource;
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
