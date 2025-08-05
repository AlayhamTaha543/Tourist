<?php

namespace App\Repositories\Interfaces;

interface LocationInterface
{
    public function showLocation($id);
    public function showAllLocation();
    public function showAllLocationFilter();
}
