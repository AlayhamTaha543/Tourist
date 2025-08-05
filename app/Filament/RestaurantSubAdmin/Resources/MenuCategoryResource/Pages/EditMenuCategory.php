<?php

namespace App\Filament\RestaurantSubAdmin\Resources\MenuCategoryResource\Pages;

use App\Filament\RestaurantSubAdmin\Resources\MenuCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMenuCategory extends EditRecord
{
    protected static string $resource = MenuCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
