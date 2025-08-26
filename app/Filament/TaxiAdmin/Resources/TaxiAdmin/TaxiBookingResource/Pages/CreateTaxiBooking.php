<?php

namespace App\Filament\TaxiAdmin\Resources\TaxiAdmin\TaxiBookingResource\Pages;

use App\Filament\TaxiAdmin\Resources\TaxiAdmin\TaxiBookingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTaxiBooking extends CreateRecord
{
    protected static string $resource = TaxiBookingResource::class;
}
