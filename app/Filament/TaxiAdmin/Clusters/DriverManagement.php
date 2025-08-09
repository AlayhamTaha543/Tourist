<?php

namespace App\Filament\TaxiAdmin\Clusters;

use Filament\Clusters\Cluster;

class DriverManagement extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Taxi Management';
    protected static ?int $navigationSort = 4;
}