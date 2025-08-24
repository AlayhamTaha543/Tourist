<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin\AdminResource\Pages;

use App\Filament\SuperAdmin\Resources\SuperAdmin\AdminResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAdmin extends CreateRecord
{
    protected static string $resource = AdminResource::class;
}
