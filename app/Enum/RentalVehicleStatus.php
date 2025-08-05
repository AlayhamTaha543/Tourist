<?php

namespace App\Enum;

enum RentalVehicleStatus: string
{
    case AVAILABLE = 'available';
    case RESERVED = 'reserved';
    case IN_MAINTENANCE = 'in_maintenance';
}
