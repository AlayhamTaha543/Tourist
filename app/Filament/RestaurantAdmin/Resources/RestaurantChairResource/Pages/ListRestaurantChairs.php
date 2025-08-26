<?php

namespace App\Filament\RestaurantAdmin\Resources\RestaurantChairResource\Pages;

use App\Filament\RestaurantAdmin\Resources\RestaurantChairResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRestaurantChairs extends ListRecords
{
    protected static string $resource = RestaurantChairResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
