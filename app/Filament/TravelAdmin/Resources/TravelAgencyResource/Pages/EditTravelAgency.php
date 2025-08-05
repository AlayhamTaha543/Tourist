<?php

namespace App\Filament\TravelAdmin\Resources\TravelAgencyResource\Pages;

use App\Filament\TravelAdmin\Resources\TravelAgencyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTravelAgency extends EditRecord
{
    protected static string $resource = TravelAgencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
