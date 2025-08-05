<?php

namespace App\Filament\RestaurantSubAdmin\Resources\MenuCategoryResource\Pages;

use App\Filament\RestaurantSubAdmin\Resources\MenuCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMenuCategory extends CreateRecord
{
    protected static string $resource = MenuCategoryResource::class;
}
