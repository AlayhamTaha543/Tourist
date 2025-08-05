<?php

namespace App\Filament\Resources\PointRuleResource\Pages;

use App\Filament\Resources\PointRuleResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPointRule extends ViewRecord
{
    protected static string $resource = PointRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
