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
                'image' => 'images/rental/r.png',
                'location_id' => 1,
                'manager_id' => 1, // Assuming admin with ID 1 exists
                'open_time' => '09:00:00',
                'close_time' => '17:00:00',
            ],
            [
                'name' => 'Metro Car Hire',
                'address' => '456 Central Avenue, Uptown',
                'image' => 'images/rental/r.png',
                'location_id' => 2,
                'manager_id' => 1,
                'open_time' => '08:00:00',
                'close_time' => '18:00:00',
            ],
            [
                'name' => 'Premium Auto Rentals',
                'address' => '789 Luxury Lane, Midtown',
                'image' => 'images/rental/r.png',
                'location_id' => 3,
                'manager_id' => 1,
                'open_time' => '10:00:00',
                'close_time' => '20:00:00',
            ],
        ];

        foreach ($offices as $office) {
            RentalOffice::updateOrCreate(
                ['name' => $office['name'], 'address' => $office['address']],
                $office
            );
        }
    }
}
