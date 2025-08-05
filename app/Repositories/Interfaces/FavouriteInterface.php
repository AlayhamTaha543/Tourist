<?php

namespace App\Repositories\Interfaces;

interface FavouriteInterface
{
    public function showFavourite($id);
    public function showAllFavourite();
    public function addRestaurantToFavourite($id);
    public function addHotelToFavourite($id);
    public function addTourToFavourite($id);
    public function addPackageToFavourite($id);
    public function removeFromFavouriteById($id);

}
