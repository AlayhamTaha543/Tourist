<?php

namespace App\Filament\RestaurantSubAdmin\Resources\MenuItemResource\Pages;

use App\Filament\RestaurantSubAdmin\Resources\MenuItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMenuItem extends ViewRecord
{
    protected static string $resource = MenuItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
