<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin\RestaurantResource\Pages;

use App\Filament\SuperAdmin\Resources\SuperAdmin\RestaurantResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRestaurant extends CreateRecord
{
    protected static string $resource = RestaurantResource::class;
}
