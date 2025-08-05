<?php

namespace App\Filament\Resources\UserRankResource\Pages;

use App\Filament\Resources\UserRankResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserRank extends EditRecord
{
    protected static string $resource = UserRankResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
