<?php

namespace App\Filament\RestaurantSubAdmin\Resources\RestaurantImageResource\Pages;

use App\Filament\RestaurantSubAdmin\Resources\RestaurantImageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRestaurantImage extends CreateRecord
{
    protected static string $resource = RestaurantImageResource::class;
}
