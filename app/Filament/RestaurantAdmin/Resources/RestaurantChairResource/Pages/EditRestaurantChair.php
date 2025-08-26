<?php

namespace App\Filament\RestaurantAdmin\Resources\RestaurantChairResource\Pages;

use App\Filament\RestaurantAdmin\Resources\RestaurantChairResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRestaurantChair extends EditRecord
{
    protected static string $resource = RestaurantChairResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
