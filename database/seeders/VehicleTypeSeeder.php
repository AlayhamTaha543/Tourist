<?php

namespace Database\Seeders;

use App\Models\VehicleType;
use Illuminate\Database\Seeder;

class VehicleTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            // City Cabs (Service ID 1)
            [
                'taxi_service_id' => 1,
                'name' => 'Standard',
                'description' => 'Regular sedan for up to 4 passengers',
                'max_passengers' => 4,
                'price_per_km' => 1.50,
                'base_price' => 3.00,
                'image_url' => 'https://example.com/vehicles/standard.png',
                'is_active' => true,
            ],
            [
                'taxi_service_id' => 1,
                'name' => 'XL',
                'description' => 'Larger vehicle for up to 6 passengers',
                'max_passengers' => 6,
                'price_per_km' => 2.00,
                'base_price' => 4.00,
                'image_url' => 'https://example.com/vehicles/xl.png',
                'is_active' => true,
            ],
            [
                'taxi_service_id' => 1,
                'name' => 'Accessible',
                'description' => 'Wheelchair accessible vehicle',
                'max_passengers' => 4,
                'price_per_km' => 1.75,
                'base_price' => 3.50,
                'image_url' => 'https://example.com/vehicles/accessible.png',
                'is_active' => true,
            ],

            // Metro Taxis (Service ID 2)
            [
                'taxi_service_id' => 2,
                'name' => 'Economy',
                'description' => 'Budget-friendly option',
                'max_passengers' => 4,
                'price_per_km' => 1.20,
                'base_price' => 2.50,
                'image_url' => 'https://example.com/vehicles/economy.png',
                'is_active' => true,
            ],
            [
                'taxi_service_id' => 2,
                'name' => 'Comfort',
                'description' => 'Mid-range comfortable ride',
                'max_passengers' => 4,
                'price_per_km' => 1.60,
                'base_price' => 3.20,
                'image_url' => 'https://example.com/vehicles/comfort.png',
                'is_active' => true,
            ],
            [
                'taxi_service_id' => 2,
                'name' => 'Van',
                'description' => 'Large capacity vehicle',
                'max_passengers' => 8,
                'price_per_km' => 2.50,
                'base_price' => 5.00,
                'image_url' => 'https://example.com/vehicles/van.png',
                'is_active' => true,
            ],

            // Elite Rides (Service ID 3)
            [
                'taxi_service_id' => 3,
                'name' => 'Business Class',
                'description' => 'Premium sedan for executives',
                'max_passengers' => 3,
                'price_per_km' => 3.00,
                'base_price' => 10.00,
                'image_url' => 'https://example.com/vehicles/business.png',
                'is_active' => true,
            ],
            [
                'taxi_service_id' => 3,
                'name' => 'Luxury',
                'description' => 'High-end luxury vehicle',
                'max_passengers' => 3,
                'price_per_km' => 4.00,
                'base_price' => 15.00,
                'image_url' => 'https://example.com/vehicles/luxury.png',
                'is_active' => true,
            ],
            [
                'taxi_service_id' => 3,
                'name' => 'VIP',
                'description' => 'Top-tier luxury with privacy',
                'max_passengers' => 2,
                'price_per_km' => 5.00,
                'base_price' => 20.00,
                'image_url' => 'https://example.com/vehicles/vip.png',
                'is_active' => true,
            ],
        ];

        foreach ($types as $type) {
            VehicleType::updateOrCreate($type);
        }
    }
}