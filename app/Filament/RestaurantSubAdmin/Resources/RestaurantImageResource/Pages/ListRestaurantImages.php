<?php

namespace App\Filament\RestaurantSubAdmin\Resources\RestaurantImageResource\Pages;

use App\Filament\RestaurantSubAdmin\Resources\RestaurantImageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRestaurantImages extends ListRecords
{
    protected static string $resource = RestaurantImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
