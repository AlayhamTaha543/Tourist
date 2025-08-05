<?php

namespace App\Http\Controllers;

use App\Interface\LocationInterface;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    protected $locationRepository;
    public function __construct(LocationInterface $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }

    public function showLocation($id)
    {
        return $this->locationRepository->showLocation($id);
    }
    public function showAllLocation()
    {
        return $this->locationRepository->showAllLocation();
    }
    public function showAllLocationFilter()
    {
        return $this->locationRepository->showAllLocationFilter();
    }
}
