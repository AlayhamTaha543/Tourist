<?php

namespace App\Providers;

use App\Repositories\Impl\Auth\AuthRepository;
use App\Repositories\Impl\BookingRepository;
use App\Repositories\Impl\FavouriteRepository;
use App\Repositories\Impl\HotelRepository;
use App\Repositories\Impl\LocationRepository;
use App\Repositories\Impl\Rent\RentalOfficeRepositoryImpl;
use App\Repositories\Impl\Rent\RentalVehicleCategoryRepositoryImpl;
use App\Repositories\Impl\Rent\RentalBookingRepositoryImpl;
use App\Repositories\Impl\Rent\RentalVehicleRepositoryImpl;
use App\Repositories\Impl\RestaurantRepository;
use App\Repositories\Impl\ServiceRepository;
use App\Repositories\Impl\TourRepository;
use App\Repositories\Impl\TravelRepository;
use App\Repositories\Interfaces\Auth\AuthInterface;
use App\Repositories\Interfaces\BookingInterface;
use App\Repositories\Interfaces\FavouriteInterface;
use App\Repositories\Interfaces\HotelInterface;
use App\Repositories\Interfaces\LocationInterface;
use App\Repositories\Interfaces\Rent\RentalOfficeRepositoryInterface;
use App\Repositories\Interfaces\Rent\RentalVehicleCategoryRepositoryInterface;
use App\Repositories\Interfaces\Rent\RentalBookingRepositoryInterface;
use App\Repositories\Interfaces\Rent\RentalVehicleRepositoryInterface;
use App\Repositories\Interfaces\RestaurantInterface;
use App\Repositories\Interfaces\ServiceInterface;
use App\Repositories\Interfaces\TourInterface;
use App\Repositories\Interfaces\TravelInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Webbingbrasil\FilamentMaps\Widgets\MapWidget;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            AuthInterface::class,
            AuthRepository::class
        );
        $this->app->bind(
            ServiceInterface::class,
            ServiceRepository::class
        );
        $this->app->bind(
            RentalOfficeRepositoryInterface::class,
            RentalOfficeRepositoryImpl::class
        );
        $this->app->bind(
            RentalVehicleCategoryRepositoryInterface::class,
            RentalVehicleCategoryRepositoryImpl::class
        );
        $this->app->bind(
            RentalVehicleRepositoryInterface::class,
            RentalVehicleRepositoryImpl::class
        );
        $this->app->bind(
            RentalBookingRepositoryInterface::class,
            RentalBookingRepositoryImpl::class
        );
        $this->app->bind(
            AuthInterface::class,
            AuthRepository::class,
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
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register all migration directories
        $migrationDirs = [database_path('migrations')];

        foreach (File::directories(database_path('migrations')) as $directory) {
            $migrationDirs[] = $directory;
        }

        $this->loadMigrationsFrom($migrationDirs);

        MapWidget::configureUsing(function (MapWidget $widget) {
            $apiKey = Config::get('services.geoapify.key');
            $widget->tileLayerUrl("https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}.png?apiKey={$apiKey}");
            $widget->tileLayerOptions([
                'attribution' => 'Powered by <a href="https://www.geoapify.com/" target="_blank">Geoapify</a> | © <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors',
            ]);
        });
    }
}
