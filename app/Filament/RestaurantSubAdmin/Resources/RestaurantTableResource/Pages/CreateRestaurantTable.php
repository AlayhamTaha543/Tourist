<?php

namespace App\Filament\RestaurantSubAdmin\Resources\RestaurantTableResource\Pages;

use App\Filament\RestaurantSubAdmin\Resources\RestaurantTableResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRestaurantTable extends CreateRecord
{
    protected static string $resource = RestaurantTableResource::class;
}
