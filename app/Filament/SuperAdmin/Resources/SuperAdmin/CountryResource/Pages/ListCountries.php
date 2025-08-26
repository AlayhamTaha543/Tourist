<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin\CountryResource\Pages;

use App\Filament\SuperAdmin\Resources\SuperAdmin\CountryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCountries extends ListRecords
{
    protected static string $resource = CountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
