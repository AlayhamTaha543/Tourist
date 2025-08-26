<?php

namespace App\Filament\RestaurantAdmin\Resources\RestaurantImageResource\Pages;

use App\Filament\RestaurantAdmin\Resources\RestaurantImageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRestaurantImage extends CreateRecord
{
    protected static string $resource = RestaurantImageResource::class;
}
