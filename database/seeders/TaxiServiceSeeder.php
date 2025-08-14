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
                'logo_url' => 'https://example.com/logos/city_cabs.png',
                'website' => 'https://citycabs.example.com',
                'phone' => '+1234567890',
                'email' => 'info@citycabs.example.com',
                'is_active' => true,
            ],
            [
                'name' => 'Metro Taxis',
                'description' => 'Fast and affordable metro area service',
                'location_id' => 2,
                'logo_url' => 'https://example.com/logos/metro_taxis.png',
                'website' => 'https://metrotaxis.example.com',
                'phone' => '+1987654321',
                'email' => 'contact@metrotaxis.example.com',
                'is_active' => true,
            ],
            [
                'name' => 'Elite Rides',
                'description' => 'Premium luxury transportation',
                'location_id' => 3,
                'logo_url' => 'https://example.com/logos/elite_rides.png',
                'website' => 'https://eliterides.example.com',
                'phone' => '+1122334455',
                'email' => 'support@eliterides.example.com',
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            TaxiService::updateOrCreate($service);
        }
    }
}
