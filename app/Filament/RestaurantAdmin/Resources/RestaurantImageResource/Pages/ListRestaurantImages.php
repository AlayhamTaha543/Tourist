<?php

namespace App\Filament\RestaurantAdmin\Resources\RestaurantImageResource\Pages;

use App\Filament\RestaurantAdmin\Resources\RestaurantImageResource;
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
