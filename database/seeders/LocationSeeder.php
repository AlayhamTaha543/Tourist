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
                'language' => 'French',
                'currency' => 'EUR',
                'description' => 'France, in Western Europe, encompasses medieval cities, alpine villages and Mediterranean beaches. Paris, its capital, is famed for its fashion houses, classical art museums including the Louvre and monuments like the Eiffel Tower.'
            ],
            [
                'name' => 'New York',
                'latitude' => 40.712776,
                'longitude' => -74.005974,
                'city' => 'New York',
                'country' => 'USA',
                'region' => 'New York',
                'is_popular' => true,
                'language' => 'English',
                'currency' => 'USD',
                'description' => 'The United States is a large country in North America with a diverse landscape, culture, and economy. It is known for its iconic cities, national parks, and influence on global culture.'
            ],
            [
                'name' => 'Tokyo',
                'latitude' => 35.689487,
                'longitude' => 139.691711,
                'city' => 'Tokyo',
                'country' => 'Japan',
                'region' => 'Kantō',
                'is_popular' => true,
                'language' => 'Japanese',
                'currency' => 'JPY',
                'description' => 'Japan is an island nation in East Asia. It is known for its vibrant cities, ancient traditions, beautiful natural landscapes, and technological advancements.'
            ],
            [
                'name' => 'Cairo',
                'latitude' => 30.044420,
                'longitude' => 31.235712,
                'city' => 'Cairo',
                'country' => 'Egypt',
                'region' => 'Cairo Governorate',
                'is_popular' => true,
                'language' => 'Arabic',
                'currency' => 'EGP',
                'description' => 'Egypt, a country linking northeast Africa with the Middle East, dates to the time of the pharaohs. Millennia-old monuments sit along the fertile Nile River Valley, including the colossal Pyramids of Giza and Great Sphinx as well as the Luxor\'s hieroglyph-lined Karnak Temple and Valley of the Kings tombs.'
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
                'language' => 'English',
                'currency' => 'USD',
                'description' => 'The United States is a large country in North America with a diverse landscape, culture, and economy. It is known for its iconic cities, national parks, and influence on global culture.'
            ],
            [
                'name' => 'Pacific Beachfront',
                'latitude' => 33.684566,
                'longitude' => -118.044740,
                'city' => 'Los Angeles',
                'country' => 'USA',
                'region' => 'California',
                'is_popular' => true,
                'language' => 'English',
                'currency' => 'USD',
                'description' => 'The United States is a large country in North America with a diverse landscape, culture, and economy. It is known for its iconic cities, national parks, and influence on global culture.'
            ],
            [
                'name' => 'Rocky Mountains',
                'latitude' => 39.550051,
                'longitude' => -105.782066,
                'city' => 'Denver',
                'country' => 'USA',
                'region' => 'Colorado',
                'is_popular' => false,
                'language' => 'English',
                'currency' => 'USD',
                'description' => 'The United States is a large country in North America with a diverse landscape, culture, and economy. It is known for its iconic cities, national parks, and influence on global culture.'
            ],
            [
                'name' => 'Urban Downtown District',
                'latitude' => 41.878113,
                'longitude' => -87.629799,
                'city' => 'Chicago',
                'country' => 'USA',
                'region' => 'Illinois',
                'is_popular' => true,
                'language' => 'English',
                'currency' => 'USD',
                'description' => 'The United States is a large country in North America with a diverse landscape, culture, and economy. It is known for its iconic cities, national parks, and influence on global culture.'
            ],
            [
                'name' => 'Historic City Center',
                'latitude' => 51.507351,
                'longitude' => -0.127758,
                'city' => 'London',
                'country' => 'UK',
                'region' => 'England',
                'is_popular' => true,
                'language' => 'English',
                'currency' => 'GBP',
                'description' => 'The United Kingdom, made up of England, Scotland, Wales and Northern Ireland, is an island nation in northwestern Europe. England – birthplace of Shakespeare and The Beatles – is home to the capital, London, a globally influential centre of finance and culture.'
            ],
        ];

        foreach ($locations as $data) {
            $country = Country::firstOrCreate(
                ['name' => $data['country']],
                [
                    'code' => strtoupper(substr($data['country'], 0, 2)),
                    'language' => $data['language'] ?? null,
                    'currency' => $data['currency'] ?? null,
                    'description' => $data['description'] ?? null,
                ]
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