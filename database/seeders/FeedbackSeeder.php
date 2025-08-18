<?php

namespace Database\Seeders;

use App\Models\FeedBack;
use App\Models\Hotel;
use App\Models\RentalOffice;
use App\Models\Restaurant;
use App\Models\TaxiService;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $restaurants = Restaurant::all();
        $hotels = Hotel::all();
        $tours = Tour::all();
        $taxiServices = TaxiService::all();
        $rentalOffices = RentalOffice::all();

        foreach ($restaurants as $restaurant) {
            FeedBack::factory()->count(2)->create([
                'feedbackable_id' => $restaurant->id,
                'feedbackable_type' => Restaurant::class,
                'user_id' => $users->random()->id,
            ]);
        }

        foreach ($hotels as $hotel) {
            FeedBack::factory()->count(2)->create([
                'feedbackable_id' => $hotel->id,
                'feedbackable_type' => Hotel::class,
                'user_id' => $users->random()->id,
            ]);
        }

        foreach ($tours as $tour) {
            FeedBack::factory()->count(2)->create([
                'feedbackable_id' => $tour->id,
                'feedbackable_type' => Tour::class,
                'user_id' => $users->random()->id,
            ]);
        }

        foreach ($taxiServices as $taxiService) {
            FeedBack::factory()->count(2)->create([
                'feedbackable_id' => $taxiService->id,
                'feedbackable_type' => TaxiService::class,
                'user_id' => $users->random()->id,
            ]);
        }

        foreach ($rentalOffices as $rentalOffice) {
            FeedBack::factory()->count(2)->create([
                'feedbackable_id' => $rentalOffice->id,
                'feedbackable_type' => RentalOffice::class,
                'user_id' => $users->random()->id,
            ]);
        }
    }
}
