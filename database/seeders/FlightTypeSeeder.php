<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FlightType;
use App\Models\TravelFlight;

class FlightTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $travelFlights = TravelFlight::all();

        $flightTypesData = [
            ['flight_type' => 'Economy', 'price' => 500.00, 'available_seats' => 150],
            ['flight_type' => 'Business', 'price' => 1200.00, 'available_seats' => 30],
            ['flight_type' => 'First Class', 'price' => 2500.00, 'available_seats' => 10],
        ];

        foreach ($travelFlights as $flight) {
            $totalAvailableSeatsForFlight = 0;
            foreach ($flightTypesData as $typeData) {
                FlightType::create([
                    'travel_flight_id' => $flight->id,
                    'flight_type' => $typeData['flight_type'],
                    'price' => $typeData['price'],
                    'available_seats' => $typeData['available_seats'],
                ]);
                $totalAvailableSeatsForFlight += $typeData['available_seats'];
            }

            // Update the available_seats for the TravelFlight
            $flight->update(['available_seats' => $totalAvailableSeatsForFlight]);
        }
    }
}