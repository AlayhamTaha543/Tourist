<?php

namespace App\Filament\RestaurantSubAdmin\Resources\ChairAvailabilityResource\Pages;

use App\Filament\RestaurantSubAdmin\Resources\ChairAvailabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChairAvailability extends EditRecord
{
    protected static string $resource = ChairAvailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
