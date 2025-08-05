<?php

namespace App\Exceptions;

use Exception;

class NoDriversAvailableException extends Exception
{
    public function __construct()
    {
        parent::__construct("No available drivers for the selected time", 404);
    }
}
