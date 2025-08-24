<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin\RestaurantResource\Pages;

use App\Filament\SuperAdmin\Resources\SuperAdmin\RestaurantResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRestaurants extends ListRecords
{
    protected static string $resource = RestaurantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
