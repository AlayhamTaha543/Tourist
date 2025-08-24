<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin\TaxiServiceResource\Pages;

use App\Filament\SuperAdmin\Resources\SuperAdmin\TaxiServiceResource;
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
