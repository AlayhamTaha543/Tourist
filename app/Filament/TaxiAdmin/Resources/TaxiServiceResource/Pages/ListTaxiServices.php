<?php

namespace App\Filament\TaxiAdmin\Resources\TaxiAdmin\TaxiServiceResource\Pages;

use App\Filament\TaxiAdmin\Resources\TaxiAdmin\TaxiServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTaxiServices extends ListRecords
{
    protected static string $resource = TaxiServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
