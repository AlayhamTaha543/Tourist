<?php

namespace App\Filament\TaxiAdmin\Resources\TaxiServiceResource\Pages;

use App\Filament\TaxiAdmin\Resources\TaxiServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTaxiService extends EditRecord
{
    protected static string $resource = TaxiServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
