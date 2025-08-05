<?php

namespace App\Enum;

enum RentalBookingStatus: String
{
    case RESERVED = 'reserved';
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}
