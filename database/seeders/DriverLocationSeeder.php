<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Driver;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DriverLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $drivers = Driver::all();

        // Define a reasonable geographical area for seeding (e.g., around Damascus)
        $minLat = 33.4;
        $maxLat = 33.6;
        $minLng = 36.2;
        $maxLng = 36.4;

        foreach ($drivers as $driver) {
            $lat = $minLat + mt_rand() / mt_getrandmax() * ($maxLat - $minLat);
            $lng = $minLng + mt_rand() / mt_getrandmax() * ($maxLng - $minLng);

            if ($driver->id === 1) {
                // Set specific coordinates for driver ID 1
                $lat = 33.5132;
                $lng = 36.2912;
            }

            // Update the driver's current_location using ST_GeomFromText for MySQL
            DB::table('drivers')
                ->where('id', $driver->id)
                ->update([
                    'current_location' => DB::raw("ST_GeomFromText('POINT({$lng} {$lat})', 4326)"),
                    'location_updated_at' => Carbon::now(),
                    'last_seen_at' => Carbon::now(),
                    'availability_status' => 'available', // Set to available for testing
                ]);
        }

        $this->command->info('Driver locations seeded successfully!');
    }
}
