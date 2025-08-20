<?php

namespace Database\Seeders;

use App\Models\PackageDestination;
use App\Models\PackageInclusion;
use App\Models\TravelAgency;
use App\Models\TravelFlight;
use App\Models\TravelPackage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TravelAgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agency1 = TravelAgency::updateOrCreate([
            'name' => 'Global Explorer Travel Agency',
            'description' => 'We provide international travel and tourism services.',
            'location_id' => 1,
            'website' => 'https://globalexplorer.com',
            'phone' => '+1 555-123-4567',
            'email' => 'contact@globalexplorer.com',
            'is_active' => true,
            'admin_id' => 9,
        ]);

        TravelFlight::updateOrCreate([
            'agency_id' => $agency1->id,
            'flight_number' => 'GE' . strtoupper(uniqid()),
            'departure_id' => 1,
            'arrival_id' => 2,
            'departure_time' => now()->addDays(3)->setTime(10, 30),
            'arrival_time' => now()->addDays(3)->setTime(14, 15),
            'duration_minutes' => 225,
            'price' => 350.00,
            'available_seats' => 120,
            'status' => 'scheduled',
        ]);
        TravelFlight::updateOrCreate([
            'agency_id' => $agency1->id,
            'flight_number' => 'GE' . strtoupper(uniqid()),
            'departure_id' => 2,
            'arrival_id' => 1,
            'departure_time' => now()->addDays(4)->setTime(18, 00),
            'arrival_time' => now()->addDays(4)->setTime(22, 45),
            'duration_minutes' => 285,
            'price' => 400.00,
            'available_seats' => 100,
            'status' => 'scheduled',
        ]);

        $package = TravelPackage::updateOrCreate([
            'agency_id' => $agency1->id,
            'name' => 'Amazing Europe Tour',
            'description' => '10-day guided tour across France, Italy, and Switzerland.',
            'duration_days' => 10,
            'base_price' => 2500.00,
            'discount_percentage' => 10,
            'max_participants' => 30,
            'average_rating' => 4.7,
            'total_ratings' => 134,
            'main_image' => 'https://example.com/images/europe-tour.jpg',
            'is_active' => true,
            'is_featured' => true,
        ]);
        PackageDestination::updateOrCreate([
            'package_id' => $package->id,
            'location_id' => 1,
            'day_number' => 3,
            'description' => 'Visit Colosseum and Vatican.',
            'duration' => '2 days',
        ]);
        PackageInclusion::updateOrCreate([
            'package_id' => $package->id,
            'inclusion_type' => 1,
            'description' => '4-star hotel with breakfast included',
            'is_highlighted' => true,
        ]);

        PackageInclusion::updateOrCreate([
            'package_id' => $package->id,
            'inclusion_type' => 2,
            'description' => 'Airport pickup and inter-city travel',
            'is_highlighted' => false,
        ]);

        $agency2 = TravelAgency::updateOrCreate([
            'name' => 'Adventure Seekers Inc.',
            'description' => 'Your gateway to thrilling adventures worldwide.',
            'location_id' => 2,
            'website' => 'https://adventureseekers.com',
            'phone' => '+1 555-987-6543',
            'email' => 'info@adventureseekers.com',
            'is_active' => true,
            'admin_id' => 9,
        ]);

        TravelFlight::updateOrCreate([
            'agency_id' => $agency2->id,
            'flight_number' => 'AS' . strtoupper(uniqid()),
            'departure_id' => 3,
            'arrival_id' => 4,
            'departure_time' => now()->addDays(5)->setTime(8, 00),
            'arrival_time' => now()->addDays(5)->setTime(11, 30),
            'duration_minutes' => 210,
            'price' => 280.00,
            'available_seats' => 80,
            'status' => 'scheduled',
        ]);
        TravelFlight::updateOrCreate([
            'agency_id' => $agency2->id,
            'flight_number' => 'AS' . strtoupper(uniqid()),
            'departure_id' => 4,
            'arrival_id' => 3,
            'departure_time' => now()->addDays(6)->setTime(15, 00),
            'arrival_time' => now()->addDays(6)->setTime(18, 30),
            'duration_minutes' => 210,
            'price' => 290.00,
            'available_seats' => 70,
            'status' => 'scheduled',
        ]);

        $agency3 = TravelAgency::updateOrCreate([
            'name' => 'Luxury Escapes Ltd.',
            'description' => 'Experience unparalleled luxury in travel.',
            'location_id' => 3,
            'website' => 'https://luxuryescapes.com',
            'phone' => '+1 555-555-5555',
            'email' => 'support@luxuryescapes.com',
            'is_active' => true,
            'admin_id' => 9,
        ]);

        TravelFlight::updateOrCreate([
            'agency_id' => $agency3->id,
            'flight_number' => 'LE' . strtoupper(uniqid()),
            'departure_id' => 5,
            'arrival_id' => 6,
            'departure_time' => now()->addDays(7)->setTime(12, 00),
            'arrival_time' => now()->addDays(7)->setTime(16, 00),
            'duration_minutes' => 240,
            'price' => 600.00,
            'available_seats' => 50,
            'status' => 'scheduled',
        ]);
        TravelFlight::updateOrCreate([
            'agency_id' => $agency3->id,
            'flight_number' => 'LE' . strtoupper(uniqid()),
            'departure_id' => 6,
            'arrival_id' => 5,
            'departure_time' => now()->addDays(8)->setTime(9, 00),
            'arrival_time' => now()->addDays(8)->setTime(13, 00),
            'duration_minutes' => 240,
            'price' => 620.00,
            'available_seats' => 45,
            'status' => 'scheduled',
        ]);
    }
}
