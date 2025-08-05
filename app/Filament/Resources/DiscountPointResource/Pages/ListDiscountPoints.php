<?php

namespace App\Filament\Resources\DiscountPointResource\Pages;

use App\Filament\Resources\DiscountPointResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDiscountPoints extends ListRecords
{
    protected static string $resource = DiscountPointResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
