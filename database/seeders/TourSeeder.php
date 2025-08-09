<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Tour;
use App\Models\TourActivity;
use App\Models\TourCategory;
use App\Models\TourSchedule;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB as FacadesDB;

class TourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category1 = TourCategory::create([
            'name' => 'Adventure',
            'description' => 'Exciting and adventurous tours for thrill seekers.',
            'icon' => 'adventure_icon.png',
            'is_active' => true,
        ]);
        $tour1 = Tour::create([
            'name' => 'Mountain Adventure',
            'description' => 'A thrilling mountain trekking experience.',
            'short_description' => 'A challenging trek through the mountains.',
            'location_id' => 1,
            'duration_hours' => 8.5,
            'duration_days' => 1,
            'base_price' => 150.00,
            'discount_percentage' => 10,
            'max_capacity' => 20,
            'min_participants' => 5,
            'difficulty_level' => 3,
            'average_rating' => 4.5,
            'total_ratings' => 100,
            'is_active' => true,
            'is_featured' => true,
            'admin_id' => 7,
        ]);

        $schedule1 = TourSchedule::create([
            'tour_id' => $tour1->id,
            'start_date' => '2025-06-01',
            'end_date' => '2025-06-01',
            'start_time' => '08:00:00',
            'available_spots' => 20,
            'price' => 150.00,
            'is_active' => true,
        ]);
        $activity1 = Activity::create([
            'name' => 'Hiking',
            'description' => 'A challenging and exciting hiking experience.',
            'image' => 'hiking_image.png',
        ]);
        $activity2 = Activity::create([
            'name' => 'Snorkeling',
            'description' => 'A fun and relaxing snorkeling adventure.',
            'image' => 'snorkeling_image.png',
        ]);
        TourActivity::create([
            'schedule_id' => $schedule1->id,
            'activity_id' => $activity1->id,
            'is_active' => true,
        ]);

        FacadesDB::table('tour_category_mapping')->insert([
            ['tour_id' => $tour1->id, 'category_id' => $category1->id],
        ]);
    }
}