<?php

namespace Database\Seeders;

use App\Models\RentalVehicle;
use Illuminate\Database\Seeder;

class RentalVehicleSeeder extends Seeder
{
    public function run()
    {
        $vehicles = [
            // City Rentals (Office ID 1)
            [
                'office_id' => 1,
                'category_id' => 1, // Economy
                'price_per_day' => 39.99,
                'license_plate' => 'CR-ECO-001',
                'make' => 'Toyota',
                'model' => 'Yaris',
                'year' => 2022,
                'seating_capacity' => 5,
                'status' => 'available',
            ],
            [
                'office_id' => 1,
                'category_id' => 1, // Economy
                'price_per_day' => 42.99,
                'license_plate' => 'CR-ECO-002',
                'make' => 'Hyundai',
                'model' => 'Accent',
                'year' => 2021,
                'seating_capacity' => 5,
                'status' => 'available',
            ],
            [
                'office_id' => 1,
                'category_id' => 1, // Economy
                'price_per_day' => 45.99,
                'license_plate' => 'CR-ECO-003',
                'make' => 'Kia',
                'model' => 'Rio',
                'year' => 2023,
                'seating_capacity' => 5,
                'status' => 'available',
            ],
            [
                'office_id' => 1,
                'category_id' => 2, // Standard
                'price_per_day' => 59.99,
                'license_plate' => 'CR-STD-001',
                'make' => 'Honda',
                'model' => 'Civic',
                'year' => 2022,
                'seating_capacity' => 5,
                'status' => 'available',
            ],
            [
                'office_id' => 1,
                'category_id' => 2, // Standard
                'price_per_day' => 62.99,
                'license_plate' => 'CR-STD-002',
                'make' => 'Toyota',
                'model' => 'Corolla',
                'year' => 2021,
                'seating_capacity' => 5,
                'status' => 'available',
            ],
            [
                'office_id' => 1,
                'category_id' => 2, // Standard
                'price_per_day' => 65.99,
                'license_plate' => 'CR-STD-003',
                'make' => 'Nissan',
                'model' => 'Sentra',
                'year' => 2023,
                'seating_capacity' => 5,
                'status' => 'available',
            ],
            [
                'office_id' => 1,
                'category_id' => 3, // Premium
                'price_per_day' => 99.99,
                'license_plate' => 'CR-PRM-001',
                'make' => 'BMW',
                'model' => '3 Series',
                'year' => 2022,
                'seating_capacity' => 5,
                'status' => 'available',
            ],
            [
                'office_id' => 1,
                'category_id' => 3, // Premium
                'price_per_day' => 109.99,
                'license_plate' => 'CR-PRM-002',
                'make' => 'Mercedes',
                'model' => 'C-Class',
                'year' => 2021,
                'seating_capacity' => 5,
                'status' => 'available',
            ],
            [
                'office_id' => 1,
                'category_id' => 3, // Premium
                'price_per_day' => 119.99,
                'license_plate' => 'CR-PRM-003',
                'make' => 'Audi',
                'model' => 'A4',
                'year' => 2023,
                'seating_capacity' => 5,
                'status' => 'available',
            ],

            // Metro Car Hire (Office ID 2)
            [
                'office_id' => 2,
                'category_id' => 1, // Economy
                'price_per_day' => 35.99,
                'license_plate' => 'MH-ECO-001',
                'make' => 'Chevrolet',
                'model' => 'Spark',
                'year' => 2022,
                'seating_capacity' => 4,
                'status' => 'available',
            ],
            [
                'office_id' => 2,
                'category_id' => 1, // Economy
                'price_per_day' => 38.99,
                'license_plate' => 'MH-ECO-002',
                'make' => 'Mitsubishi',
                'model' => 'Mirage',
                'year' => 2021,
                'seating_capacity' => 5,
                'status' => 'available',
            ],
            [
                'office_id' => 2,
                'category_id' => 1, // Economy
                'price_per_day' => 41.99,
                'license_plate' => 'MH-ECO-003',
                'make' => 'Ford',
                'model' => 'Fiesta',
                'year' => 2023,
                'seating_capacity' => 5,
                'status' => 'available',
            ],
            [
                'office_id' => 2,
                'category_id' => 2, // Standard
                'price_per_day' => 55.99,
                'license_plate' => 'MH-STD-001',
                'make' => 'Volkswagen',
                'model' => 'Jetta',
                'year' => 2022,
                'seating_capacity' => 5,
                'status' => 'available',
            ],
            [
                'office_id' => 2,
                'category_id' => 2, // Standard
                'price_per_day' => 58.99,
                'license_plate' => 'MH-STD-002',
                'make' => 'Subaru',
                'model' => 'Impreza',
                'year' => 2021,
                'seating_capacity' => 5,
                'status' => 'available',
            ],
            [
                'office_id' => 2,
                'category_id' => 2, // Standard
                'price_per_day' => 61.99,
                'license_plate' => 'MH-STD-003',
                'make' => 'Mazda',
                'model' => '3',
                'year' => 2023,
                'seating_capacity' => 5,
                'status' => 'available',
            ],
            [
                'office_id' => 2,
                'category_id' => 3, // Premium
                'price_per_day' => 89.99,
                'license_plate' => 'MH-PRM-001',
                'make' => 'Lexus',
                'model' => 'IS',
                'year' => 2022,
                'seating_capacity' => 5,
                'status' => 'available',
            ],
            [
                'office_id' => 2,
                'category_id' => 3, // Premium
                'price_per_day' => 99.99,
                'license_plate' => 'MH-PRM-002',
                'make' => 'Infiniti',
                'model' => 'Q50',
                'year' => 2021,
                'seating_capacity' => 5,
                'status' => 'available',
            ],
            [
                'office_id' => 2,
                'category_id' => 3, // Premium
                'price_per_day' => 109.99,
                'license_plate' => 'MH-PRM-003',
                'make' => 'Genesis',
                'model' => 'G70',
                'year' => 2023,
                'seating_capacity' => 5,
                'status' => 'available',
            ],

            // Premium Auto Rentals (Office ID 3)
            [
                'office_id' => 3,
                'category_id' => 1, // Economy
                'price_per_day' => 45.99,
                'license_plate' => 'PA-ECO-001',
                'make' => 'Toyota',
                'model' => 'Prius',
                'year' => 2022,
                'seating_capacity' => 5,
                'status' => 'available',
            ],
            [
                'office_id' => 3,
                'category_id' => 1, // Economy
                'price_per_day' => 48.99,
                'license_plate' => 'PA-ECO-002',
                'make' => 'Honda',
                'model' => 'Insight',
                'year' => 2021,
                'seating_capacity' => 5,
                'status' => 'available',
            ],
            [
                'office_id' => 3,
                'category_id' => 1, // Economy
                'price_per_day' => 51.99,
                'license_plate' => 'PA-ECO-003',
                'make' => 'Hyundai',
                'model' => 'Ioniq',
                'year' => 2023,
                'seating_capacity' => 5,
                'status' => 'available',
            ],
            [
                'office_id' => 3,
                'category_id' => 2, // Standard
                'price_per_day' => 69.99,
                'license_plate' => 'PA-STD-001',
                'make' => 'Volvo',
                'model' => 'S60',
                'year' => 2022,
                'seating_capacity' => 5,
                'status' => 'available',
            ],
            [
                'office_id' => 3,
                'category_id' => 2, // Standard
                'price_per_day' => 72.99,
                'license_plate' => 'PA-STD-002',
                'make' => 'Acura',
                'model' => 'TLX',
                'year' => 2021,
                'seating_capacity' => 5,
                'status' => 'available',
            ],
            [
                'office_id' => 3,
                'category_id' => 2, // Standard
                'price_per_day' => 75.99,
                'license_plate' => 'PA-STD-003',
                'make' => 'Alfa Romeo',
                'model' => 'Giulia',
                'year' => 2023,
                'seating_capacity' => 5,
                'status' => 'available',
            ],
            [
                'office_id' => 3,
                'category_id' => 3, // Premium
                'price_per_day' => 149.99,
                'license_plate' => 'PA-PRM-001',
                'make' => 'Porsche',
                'model' => 'Panamera',
                'year' => 2022,
                'seating_capacity' => 4,
                'status' => 'available',
            ],
            [
                'office_id' => 3,
                'category_id' => 3, // Premium
                'price_per_day' => 179.99,
                'license_plate' => 'PA-PRM-002',
                'make' => 'Mercedes',
                'model' => 'S-Class',
                'year' => 2021,
                'seating_capacity' => 5,
                'status' => 'available',
            ],
            [
                'office_id' => 3,
                'category_id' => 3, // Premium
                'price_per_day' => 199.99,
                'license_plate' => 'PA-PRM-003',
                'make' => 'BMW',
                'model' => '7 Series',
                'year' => 2023,
                'seating_capacity' => 5,
                'status' => 'available',
            ],
        ];

        foreach ($vehicles as $vehicle) {
            RentalVehicle::create($vehicle);
        }
    }
}
