<?php

namespace App\Http\Controllers;

use App\Interface\FavouriteInterface;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{
    protected $favouriteRepository;
    public function __construct(FavouriteInterface $favouriteRepository)
    {
        $this->favouriteRepository = $favouriteRepository;
    }
    public function showAllFavourite(){
        return $this->favouriteRepository->showAllFavourite();
    }
    public function showFavourite($id){
        return $this->favouriteRepository->showFavourite($id);
    }
    public function addRestaurantToFavourite($id){
        return $this->favouriteRepository->addRestaurantToFavourite($id);
    }
    public function addHotelToFavourite($id){
        return $this->favouriteRepository->addHotelToFavourite($id);
    }
    public function addTourToFavourite($id){
        return $this->favouriteRepository->addTourToFavourite($id);
    }
    public function addPackageToFavourite($id){
        return $this->favouriteRepository->addPackageToFavourite($id);
    }
    public function removeFromFavouriteById($id){
        return $this->favouriteRepository->removeFromFavouriteById($id);
    }
}
