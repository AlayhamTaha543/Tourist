<?php

namespace App\Filament\TravelAdmin\Resources\TravelAgencyResource\Pages;

use App\Filament\TravelAdmin\Resources\TravelAgencyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTravelAgencies extends ListRecords
{
    protected static string $resource = TravelAgencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
