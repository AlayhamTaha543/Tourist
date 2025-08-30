<?php

namespace App\Filament\TravelSubAdmin\Resources\TravelAgencyResource\Pages;

use App\Filament\TravelSubAdmin\Resources\TravelAgencyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTravelAgency extends EditRecord
{
    protected static string $resource = TravelAgencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
