<?php

namespace App\Filament\RestaurantSubAdmin\Resources\MenuCategoryResource\Pages;

use App\Filament\RestaurantSubAdmin\Resources\MenuCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMenuCategory extends ViewRecord
{
    protected static string $resource = MenuCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
