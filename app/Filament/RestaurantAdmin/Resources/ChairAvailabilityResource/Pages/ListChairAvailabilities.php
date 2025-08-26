<?php

namespace App\Filament\RestaurantAdmin\Resources\ChairAvailabilityResource\Pages;

use App\Filament\RestaurantAdmin\Resources\ChairAvailabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChairAvailabilities extends ListRecords
{
    protected static string $resource = ChairAvailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
