<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            // Original locations
            [
                'name' => 'Paris',
                'latitude' => 48.856613,
                'longitude' => 2.352222,
                'city' => 'Paris',
                'country' => 'France',
                'region' => 'Île-de-France',
                'is_popular' => true,
            ],
            [
                'name' => 'New York',
                'latitude' => 40.712776,
                'longitude' => -74.005974,
                'city' => 'New York',
                'country' => 'USA',
                'region' => 'New York',
                'is_popular' => true,
            ],
            [
                'name' => 'Tokyo',
                'latitude' => 35.689487,
                'longitude' => 139.691711,
                'city' => 'Tokyo',
                'country' => 'Japan',
                'region' => 'Kantō',
                'is_popular' => true,
            ],
            [
                'name' => 'Cairo',
                'latitude' => 30.044420,
                'longitude' => 31.235712,
                'city' => 'Cairo',
                'country' => 'Egypt',
                'region' => 'Cairo Governorate',
                'is_popular' => true,
            ],

            // Locations from HotelSeeder (1-5)
            [
                'name' => 'Downtown City Center',
                'latitude' => 40.758896,
                'longitude' => -73.985130,
                'city' => 'New York',
                'country' => 'USA',
                'region' => 'New York',
                'is_popular' => true,
            ],
            [
                'name' => 'Pacific Beachfront',
                'latitude' => 33.684566,
                'longitude' => -118.044740,
                'city' => 'Los Angeles',
                'country' => 'USA',
                'region' => 'California',
                'is_popular' => true,
            ],
            [
                'name' => 'Rocky Mountains',
                'latitude' => 39.550051,
                'longitude' => -105.782066,
                'city' => 'Denver',
                'country' => 'USA',
                'region' => 'Colorado',
                'is_popular' => false,
            ],
            [
                'name' => 'Urban Downtown District',
                'latitude' => 41.878113,
                'longitude' => -87.629799,
                'city' => 'Chicago',
                'country' => 'USA',
                'region' => 'Illinois',
                'is_popular' => true,
            ],
            [
                'name' => 'Historic City Center',
                'latitude' => 51.507351,
                'longitude' => -0.127758,
                'city' => 'London',
                'country' => 'UK',
                'region' => 'England',
                'is_popular' => true,
            ],
        ];

        foreach ($locations as $data) {
            $country = Country::firstOrCreate(
                ['name' => $data['country']],
                ['code' => strtoupper(substr($data['country'], 0, 2))]
            );

            $city = City::firstOrCreate(
                ['name' => $data['city'], 'country_id' => $country->id]
            );

            Location::updateOrCreate(
                ['name' => $data['name']],
                [
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'city_id' => $city->id,
                    'region' => $data['region'],
                    'is_popular' => $data['is_popular'] ?? false,
                ]
            );
        }
    }
}
