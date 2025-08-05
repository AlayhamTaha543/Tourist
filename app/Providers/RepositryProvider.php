<?php

namespace App\Providers;

use App\Http\Controllers\BookingController;
use App\Interface\AuthInterface;
use App\Interface\BookingInterface;
use App\Interface\FavouriteInterface;
use App\Interface\HotelInterface;
use App\Interface\LocationInterface;
use App\Interface\PackageInterface;
use App\Interface\RestaurantInterface;
use App\Interface\TourInterface;
use App\Interface\TravelInterface;
use App\Repositories\authRepository;
use App\Repositories\BookingRepository;
use App\Repositories\FavouriteRepository;
use App\Repositories\HotelRepository;
use App\Repositories\LocationRepository;
use App\Repositories\PackageRepository;
use App\Repositories\RestaurantRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\TourRepository;
use App\Repositories\TravelRepository;
use App\Interface\ServiceInterface;
use Illuminate\Support\ServiceProvider;

class RepositryProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            AuthInterface::class,
            authRepository::class,
        );
        $this->app->bind(
            RestaurantInterface::class,
            RestaurantRepository::class,
        );
        $this->app->bind(
            HotelInterface::class,
            HotelRepository::class,
        );
        $this->app->bind(
            TourInterface::class,
            TourRepository::class,
        );
        $this->app->bind(
            FavouriteInterface::class,
            FavouriteRepository::class,
        );
        $this->app->bind(
            TravelInterface::class,
            TravelRepository::class,
        );
        $this->app->bind(
            BookingInterface::class,
            BookingRepository::class,
        );
        $this->app->bind(
            LocationInterface::class,
            LocationRepository::class,
        );
        $this->app->bind(
            ServiceInterface::class,
            ServiceRepository::class,
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
