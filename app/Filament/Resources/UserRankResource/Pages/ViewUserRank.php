<?php

namespace App\Filament\Resources\UserRankResource\Pages;

use App\Filament\Resources\UserRankResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUserRank extends ViewRecord
{
    protected static string $resource = UserRankResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
