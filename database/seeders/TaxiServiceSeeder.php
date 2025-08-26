<?php

namespace Database\Seeders;

use App\Models\TaxiService;
use Illuminate\Database\Seeder;

class TaxiServiceSeeder extends Seeder
{
    public function run()
    {
        $services = [
            [
                'name' => 'City Cabs',
                'description' => 'Reliable city-wide taxi service',
                'location_id' => 1,
                'logo_url' => 'images/taxi/t.png',
                'website' => 'https://citycabs.example.com',
                'phone' => '+1234567890',
                'email' => 'info@citycabs.example.com',
                'is_active' => true,
                'open_time' => '06:00:00',
                'close_time' => '22:00:00',
            ],
            [
                'name' => 'Metro Taxis',
                'description' => 'Fast and affordable metro area service',
                'location_id' => 2,
                'logo_url' => 'images/taxi/t.png',
                'website' => 'https://metrotaxis.example.com',
                'phone' => '+1987654321',
                'email' => 'contact@metrotaxis.example.com',
                'is_active' => true,
                'open_time' => '07:00:00',
                'close_time' => '23:00:00',
            ],
            [
                'name' => 'Elite Rides',
                'description' => 'Premium luxury transportation',
                'location_id' => 3,
                'logo_url' => 'images/taxi/t.png',
                'website' => 'https://eliterides.example.com',
                'phone' => '+1122334455',
                'email' => 'support@eliterides.example.com',
                'is_active' => true,
                'open_time' => '08:00:00',
                'close_time' => '00:00:00',
            ],
        ];

        foreach ($services as $service) {
            TaxiService::updateOrCreate(
                ['name' => $service['name'], 'email' => $service['email']],
                $service
            );
        }
    }
}
