<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin\CityResource\Pages;

use App\Filament\SuperAdmin\Resources\SuperAdmin\CityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCities extends ListRecords
{
    protected static string $resource = CityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
