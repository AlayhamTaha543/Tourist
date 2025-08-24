<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin\RentalOfficeResource\Pages;

use App\Filament\SuperAdmin\Resources\SuperAdmin\RentalOfficeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRentalOffices extends ListRecords
{
    protected static string $resource = RentalOfficeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
