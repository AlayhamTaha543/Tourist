<?php

namespace App\Filament\RestaurantSubAdmin\Resources\RestaurantTableResource\Pages;

use App\Filament\RestaurantSubAdmin\Resources\RestaurantTableResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRestaurantTable extends EditRecord
{
    protected static string $resource = RestaurantTableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
