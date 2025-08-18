<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            LocationSeeder::class,
            TravelAgencySeeder::class,
            RestaurantSeeder::class,
            TourSeeder::class,
            HotelSeeder::class,
            TaxiServiceSeeder::class,
            VehicleTypeSeeder::class,
            VehicleSeeder::class,
            DriverSeeder::class,
            DriverVehicleAssignmentSeeder::class,
            RentalOfficeSeeder::class,
            RentalVehicleCategorySeeder::class,
            RentalVehicleSeeder::class,
            RentalVehicleStatusHistorySeeder::class,
            UserSeeder::class,
            FeedbackSeeder::class,
        ]);
    }
}
