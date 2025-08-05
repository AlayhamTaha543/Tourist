<?php

namespace App\Filament\RestaurantSubAdmin\Resources\RestaurantImageResource\Pages;

use App\Filament\RestaurantSubAdmin\Resources\RestaurantImageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRestaurantImage extends EditRecord
{
    protected static string $resource = RestaurantImageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
