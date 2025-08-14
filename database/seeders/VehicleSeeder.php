<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run()
    {
        $vehicles = [
            // City Cabs vehicles (Service ID 1)
            [
                'taxi_service_id' => 1,
                'vehicle_type_id' => 1, // Standard
                'registration_number' => 'CC-1001',
                'model' => 'Toyota Camry',
                'year' => 2020,
                'color' => 'White',
                'is_active' => true,
            ],
            [
                'taxi_service_id' => 1,
                'vehicle_type_id' => 2, // XL
                'registration_number' => 'CC-2001',
                'model' => 'Ford Explorer',
                'year' => 2019,
                'color' => 'Black',
                'is_active' => true,
            ],
            [
                'taxi_service_id' => 1,
                'vehicle_type_id' => 3, // Accessible
                'registration_number' => 'CC-3001',
                'model' => 'BraunAbility MV-1',
                'year' => 2021,
                'color' => 'Blue',
                'is_active' => true,
            ],

            // Metro Taxis vehicles (Service ID 2)
            [
                'taxi_service_id' => 2,
                'vehicle_type_id' => 4, // Economy
                'registration_number' => 'MT-1001',
                'model' => 'Honda Civic',
                'year' => 2018,
                'color' => 'Silver',
                'is_active' => true,
            ],
            [
                'taxi_service_id' => 2,
                'vehicle_type_id' => 5, // Comfort
                'registration_number' => 'MT-2001',
                'model' => 'Hyundai Sonata',
                'year' => 2020,
                'color' => 'Gray',
                'is_active' => true,
            ],
            [
                'taxi_service_id' => 2,
                'vehicle_type_id' => 6, // Van
                'registration_number' => 'MT-3001',
                'model' => 'Chevrolet Express',
                'year' => 2019,
                'color' => 'Red',
                'is_active' => true,
            ],

            // Elite Rides vehicles (Service ID 3)
            [
                'taxi_service_id' => 3,
                'vehicle_type_id' => 7, // Business Class
                'registration_number' => 'ER-1001',
                'model' => 'Mercedes E-Class',
                'year' => 2022,
                'color' => 'Black',
                'is_active' => true,
            ],
            [
                'taxi_service_id' => 3,
                'vehicle_type_id' => 8, // Luxury
                'registration_number' => 'ER-2001',
                'model' => 'BMW 7 Series',
                'year' => 2021,
                'color' => 'Dark Blue',
                'is_active' => true,
            ],
            [
                'taxi_service_id' => 3,
                'vehicle_type_id' => 9, // VIP
                'registration_number' => 'ER-3001',
                'model' => 'Rolls-Royce Phantom',
                'year' => 2023,
                'color' => 'Silver',
                'is_active' => true,
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::updateOrCreate($vehicle);
        }
    }
}