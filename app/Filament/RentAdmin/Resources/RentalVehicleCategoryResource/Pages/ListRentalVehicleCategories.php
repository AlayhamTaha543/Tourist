<?php

namespace App\Filament\RentAdmin\Resources\RentalVehicleCategoryResource\Pages;

use App\Filament\RentAdmin\Resources\RentalVehicleCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRentalVehicleCategories extends ListRecords
{
    protected static string $resource = RentalVehicleCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
