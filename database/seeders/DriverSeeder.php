<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DriverSeeder extends Seeder
{
    public function run()
    {
        $drivers = [
            // City Cabs drivers (Service ID 1)
            [
                'taxi_service_id' => 1,
                'license_number' => 'DL-CC-001',
                'experience_years' => 5,
                'rating' => 4.5,
                'is_active' => true,
                'admin_email' => 'driver1@taxi.com',
                'lat' => 24.7136,
                'lng' => 46.6753,
            ],
            [
                'taxi_service_id' => 1,
                'license_number' => 'DL-CC-002',
                'experience_years' => 3,
                'rating' => 4.2,
                'is_active' => true,
                'admin_email' => 'driver2@taxi.com',
                'lat' => 24.7140,
                'lng' => 46.6760,
            ],
            [
                'taxi_service_id' => 1,
                'license_number' => 'DL-CC-003',
                'experience_years' => 7,
                'rating' => 4.8,
                'is_active' => true,
                'admin_email' => 'driver3@taxi.com',
                'lat' => 24.7150,
                'lng' => 46.6770,
            ],

            // Metro Taxis drivers (Service ID 2)
            [
                'taxi_service_id' => 2,
                'license_number' => 'DL-MT-001',
                'experience_years' => 4,
                'rating' => 4.3,
                'is_active' => true,
                'admin_email' => 'driver4@taxi.com',
                'lat' => 24.7200,
                'lng' => 46.6800,
            ],
            [
                'taxi_service_id' => 2,
                'license_number' => 'DL-MT-002',
                'experience_years' => 2,
                'rating' => 4.0,
                'is_active' => true,
                'admin_email' => 'driver5@taxi.com',
                'lat' => 24.7210,
                'lng' => 46.6810,
            ],
            [
                'taxi_service_id' => 2,
                'license_number' => 'DL-MT-003',
                'experience_years' => 6,
                'rating' => 4.7,
                'is_active' => true,
                'admin_email' => 'driver6@taxi.com',
                'lat' => 24.7220,
                'lng' => 46.6820,
            ],

            // Elite Rides drivers (Service ID 3)
            [
                'taxi_service_id' => 3,
                'license_number' => 'DL-ER-001',
                'experience_years' => 8,
                'rating' => 4.9,
                'is_active' => true,
                'admin_email' => 'driver7@taxi.com',
                'lat' => 24.7300,
                'lng' => 46.6900,
            ],
            [
                'taxi_service_id' => 3,
                'license_number' => 'DL-ER-002',
                'experience_years' => 10,
                'rating' => 5.0,
                'is_active' => true,
                'admin_email' => 'driver8@taxi.com',
                'lat' => 24.7310,
                'lng' => 46.6910,
            ],
            [
                'taxi_service_id' => 3,
                'license_number' => 'DL-ER-003',
                'experience_years' => 5,
                'rating' => 4.8,
                'is_active' => true,
                'admin_email' => 'driver9@taxi.com',
                'lat' => 24.7320,
                'lng' => 46.6920,
            ],
        ];

        foreach ($drivers as $driverData) {
            $admin = Admin::where('email', $driverData['admin_email'])->first();

            Driver::updateOrCreate(
                ['license_number' => $driverData['license_number']],
                [
                    'taxi_service_id' => $driverData['taxi_service_id'],
                    'experience_years' => $driverData['experience_years'],
                    'rating' => $driverData['rating'],
                    'is_active' => $driverData['is_active'],
                    'admin_id' => $admin ? $admin->id : null,
                    'current_location' => DB::raw("ST_GeomFromText('POINT({$driverData['lng']} {$driverData['lat']})')"),
                ]
            );
        }
    }
}
