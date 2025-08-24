<?php

namespace App\Filament\SuperAdmin\Resources\SuperAdmin\UserResource\Pages;

use App\Filament\SuperAdmin\Resources\SuperAdmin\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
