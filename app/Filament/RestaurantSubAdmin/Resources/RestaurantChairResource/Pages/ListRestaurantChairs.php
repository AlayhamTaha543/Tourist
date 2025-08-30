<?php

namespace App\Filament\RestaurantSubAdmin\Resources\RestaurantChairResource\Pages;

use App\Filament\RestaurantSubAdmin\Resources\RestaurantChairResource;
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
