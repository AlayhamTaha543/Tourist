<?php

namespace App\Filament\RestaurantSubAdmin\Resources\MenuItemResource\Pages;

use App\Filament\RestaurantSubAdmin\Resources\MenuItemResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMenuItem extends CreateRecord
{
    protected static string $resource = MenuItemResource::class;
}
