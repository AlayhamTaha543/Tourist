<?php

namespace App\Filament\Resources\PointRuleResource\Pages;

use App\Filament\Resources\PointRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPointRule extends EditRecord
{
    protected static string $resource = PointRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
