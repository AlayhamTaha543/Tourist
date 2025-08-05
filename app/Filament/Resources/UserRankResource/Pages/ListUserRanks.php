<?php

namespace App\Filament\Resources\UserRankResource\Pages;

use App\Filament\Resources\UserRankResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserRanks extends ListRecords
{
    protected static string $resource = UserRankResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
