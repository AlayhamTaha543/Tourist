<?php

namespace App\Filament\TravelAdmin\Resources\TravelAgencyResource\Pages;

use App\Filament\TravelAdmin\Resources\TravelAgencyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTravelAgency extends ViewRecord
{
    protected static string $resource = TravelAgencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
