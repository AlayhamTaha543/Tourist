<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DriverVehicleAssignmentSeeder extends Seeder
{
    public function run()
    {
        // Assign drivers to vehicles (1 driver per vehicle)
        for ($i = 1; $i <= 9; $i++) {
            DB::table('driver_vehicle_assignments')->insert([
                'driver_id' => $i,
                'vehicle_id' => $i,
                'assigned_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
