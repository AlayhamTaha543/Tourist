<?php

namespace Database\Seeders;

use App\Models\RentalOffice;
use Illuminate\Database\Seeder;

class RentalOfficeSeeder extends Seeder
{
    public function run()
    {
        $offices = [
            [
                'name' => 'City Rentals',
                'address' => '123 Main Street, Downtown',
                'location_id' => 1,
                'manager_id' => 1, // Assuming admin with ID 1 exists
            ],
            [
                'name' => 'Metro Car Hire',
                'address' => '456 Central Avenue, Uptown',
                'location_id' => 2,
                'manager_id' => 1,
            ],
            [
                'name' => 'Premium Auto Rentals',
                'address' => '789 Luxury Lane, Midtown',
                'location_id' => 3,
                'manager_id' => 1,
            ],
        ];

        foreach ($offices as $office) {
            RentalOffice::create($office);
        }
    }
}
