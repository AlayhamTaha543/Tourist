<?php

namespace Database\Seeders;

use App\Models\RentalVehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RentalVehicleStatusHistorySeeder extends Seeder
{
    public function run()
    {
        $vehicles = RentalVehicle::all();

        foreach ($vehicles as $vehicle) {
            // Add a status change record for each vehicle
            DB::table('rental_vehicle_status_history')->insert([
                'vehicle_id' => $vehicle->id,
                'old_status' => 'in_maintenance', // Assume all vehicles came from maintenance
                'new_status' => 'available', // Then became available
                'changed_by_id' => 1, // Assuming admin ID 1
                'changed_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }
}
