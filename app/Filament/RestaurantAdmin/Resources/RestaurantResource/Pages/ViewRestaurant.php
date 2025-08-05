<?php

namespace App\Filament\RestaurantAdmin\Resources\RestaurantResource\Pages;

use App\Filament\RestaurantAdmin\Resources\RestaurantResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRestaurant extends ViewRecord
{
    protected static string $resource = RestaurantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
