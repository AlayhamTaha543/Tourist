<?php

namespace App\Filament\TravelAdmin\Resources\TravelBookingResource\Pages;

use App\Filament\TravelAdmin\Resources\TravelBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTravelBooking extends ViewRecord
{
    protected static string $resource = TravelBookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
