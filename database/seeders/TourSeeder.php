<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Tour;
use App\Models\TourActivity;
use App\Models\TourCategory;
use App\Models\TourImage;
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
        $category1 = TourCategory::updateOrCreate([
            'name' => 'Adventure',
            'description' => 'Exciting and adventurous tours for thrill seekers.',
            'icon' => 'adventure_icon.png',
            'is_active' => true,
        ]);
        $tour1 = Tour::updateOrCreate(
            ['name' => 'Mountain Adventure'],
            [
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
                'language' => 'en',
            ]
        );

        // Seed Tour Images for tour1
        TourImage::updateOrCreate([
            'tour_id' => $tour1->id,
            'image' => 'images/tour/1.jpg',
            'display_order' => 1,
        ]);
        TourImage::updateOrCreate([
            'tour_id' => $tour1->id,
            'image' => 'images/tour/2.jpg',
            'display_order' => 2,
        ]);
        TourImage::updateOrCreate([
            'tour_id' => $tour1->id,
            'image' => 'images/tour/3.jpg',
            'display_order' => 3,
        ]);

        $schedule1 = TourSchedule::updateOrCreate([
            'tour_id' => $tour1->id,
            'start_date' => '2025-06-01',
            'end_date' => '2025-06-01',
            'start_time' => '08:00:00',
            'available_spots' => 20,
            'price' => 150.00,
            'is_active' => true,
        ]);
        $activity1 = Activity::updateOrCreate([
            'name' => 'Hiking',
            'description' => 'A challenging and exciting hiking experience.',
            'image' => 'hiking_image.png',
        ]);
        $activity2 = Activity::updateOrCreate([
            'name' => 'Snorkeling',
            'description' => 'A fun and relaxing snorkeling adventure.',
            'image' => 'snorkeling_image.png',
        ]);
        TourActivity::updateOrCreate([
            'schedule_id' => $schedule1->id,
            'activity_id' => $activity1->id,
            'is_active' => true,
        ]);

        FacadesDB::table('tour_category_mapping')->insert([
            ['tour_id' => $tour1->id, 'category_id' => $category1->id],
        ]);

        $tour2 = Tour::updateOrCreate(
            ['name' => 'Coastal Kayaking'],
            [
                'description' => 'Explore the beautiful coastline by kayak.',
                'short_description' => 'A scenic kayaking tour.',
                'location_id' => 2,
                'duration_hours' => 4,
                'duration_days' => 0,
                'base_price' => 80.00,
                'discount_percentage' => 5,
                'max_capacity' => 15,
                'min_participants' => 2,
                'difficulty_level' => 2,
                'average_rating' => 4.8,
                'total_ratings' => 75,
                'is_active' => true,
                'is_featured' => false,
                'admin_id' => 7,
                'language' => 'en',
            ]
        );

        $schedule2 = TourSchedule::updateOrCreate([
            'tour_id' => $tour2->id,
            'start_date' => '2025-07-10',
            'end_date' => '2025-07-10',
            'start_time' => '10:00:00',
            'available_spots' => 15,
            'price' => 80.00,
            'is_active' => true,
        ]);

        TourActivity::updateOrCreate([
            'schedule_id' => $schedule2->id,
            'activity_id' => $activity2->id,
            'is_active' => true,
        ]);

        FacadesDB::table('tour_category_mapping')->insert([
            ['tour_id' => $tour2->id, 'category_id' => $category1->id],
        ]);

        $tour3 = Tour::updateOrCreate(
            ['name' => 'Historic City Walk'],
            [
                'description' => 'A guided walk through the historic parts of the city.',
                'short_description' => 'Discover the city\'s history.',
                'location_id' => 3,
                'duration_hours' => 3,
                'duration_days' => 0,
                'base_price' => 50.00,
                'discount_percentage' => 0,
                'max_capacity' => 25,
                'min_participants' => 4,
                'difficulty_level' => 1,
                'average_rating' => 4.6,
                'total_ratings' => 120,
                'is_active' => true,
                'is_featured' => true,
                'admin_id' => 7,
                'language' => 'en',
            ]
        );

        $schedule3 = TourSchedule::updateOrCreate([
            'tour_id' => $tour3->id,
            'start_date' => '2025-08-05',
            'end_date' => '2025-08-05',
            'start_time' => '09:30:00',
            'available_spots' => 25,
            'price' => 50.00,
            'is_active' => true,
        ]);

        $activity3 = Activity::updateOrCreate([
            'name' => 'City Tour',
            'description' => 'A guided tour of the city\'s main attractions.',
            'image' => 'city_tour_image.png',
        ]);

        TourActivity::updateOrCreate([
            'schedule_id' => $schedule3->id,
            'activity_id' => $activity3->id,
            'is_active' => true,
        ]);

        FacadesDB::table('tour_category_mapping')->insert([
            ['tour_id' => $tour3->id, 'category_id' => $category1->id],
        ]);

        // Seed Tour Guide Skills
        \App\Models\TourGuideSkill::updateOrCreate(
            ['admin_id' => 7],
            ['skills' => ['funny', 'sociable', 'knowledgeable', 'energetic'], 'age' => 30]
        );
        \App\Models\TourGuideSkill::updateOrCreate(
            ['admin_id' => 6],
            ['skills' => ['funny', 'sociable', 'handsum'], 'age' => 30]
        );
        // Tours for admin_id = 6
        $tour4 = Tour::updateOrCreate(
            ['name' => 'Desert Safari'],
            [
                'description' => 'An exciting desert safari adventure.',
                'short_description' => 'Explore the dunes and enjoy a desert camp.',
                'location_id' => 3,
                'duration_hours' => 6,
                'duration_days' => 0,
                'base_price' => 120.00,
                'discount_percentage' => 15,
                'max_capacity' => 30,
                'min_participants' => 10,
                'difficulty_level' => 2,
                'average_rating' => 4.7,
                'total_ratings' => 90,
                'is_active' => true,
                'is_featured' => true,
                'admin_id' => 6,
                'language' => 'en',
            ]
        );

        // Seed Tour Images for tour4 (same as tour1)
        TourImage::updateOrCreate([
            'tour_id' => $tour4->id,
            'image' => 'images/tour/1.jpg',
            'display_order' => 1,
        ]);
        TourImage::updateOrCreate([
            'tour_id' => $tour4->id,
            'image' => 'images/tour/2.jpg',
            'display_order' => 2,
        ]);
        TourImage::updateOrCreate([
            'tour_id' => $tour4->id,
            'image' => 'images/tour/3.jpg',
            'display_order' => 3,
        ]);

        $schedule4 = TourSchedule::updateOrCreate([
            'tour_id' => $tour4->id,
            'start_date' => '2025-09-01',
            'end_date' => '2025-09-01',
            'start_time' => '14:00:00',
            'available_spots' => 30,
            'price' => 120.00,
            'is_active' => true,
        ]);

        TourActivity::updateOrCreate([
            'schedule_id' => $schedule4->id,
            'activity_id' => $activity1->id, // Using Hiking activity
            'is_active' => true,
        ]);

        FacadesDB::table('tour_category_mapping')->insert([
            ['tour_id' => $tour4->id, 'category_id' => $category1->id],
        ]);

        $tour5 = Tour::updateOrCreate(
            ['name' => 'Underwater Discovery'],
            [
                'description' => 'Discover the vibrant marine life.',
                'short_description' => 'Snorkeling and diving in crystal clear waters.',
                'location_id' => 5,
                'duration_hours' => 5,
                'duration_days' => 0,
                'base_price' => 100.00,
                'discount_percentage' => 10,
                'max_capacity' => 12,
                'min_participants' => 3,
                'difficulty_level' => 2,
                'average_rating' => 4.9,
                'total_ratings' => 60,
                'is_active' => true,
                'is_featured' => false,
                'admin_id' => 6,
                'language' => 'en',
            ]
        );

        // Seed Tour Images for tour5 (same as tour1)
        TourImage::updateOrCreate([
            'tour_id' => $tour5->id,
            'image' => 'images/tour/1.jpg',
            'display_order' => 1,
        ]);
        TourImage::updateOrCreate([
            'tour_id' => $tour5->id,
            'image' => 'images/tour/2.jpg',
            'display_order' => 2,
        ]);
        TourImage::updateOrCreate([
            'tour_id' => $tour5->id,
            'image' => 'images/tour/3.jpg',
            'display_order' => 3,
        ]);

        $schedule5 = TourSchedule::updateOrCreate([
            'tour_id' => $tour5->id,
            'start_date' => '2025-10-15',
            'end_date' => '2025-10-15',
            'start_time' => '09:00:00',
            'available_spots' => 12,
            'price' => 100.00,
            'is_active' => true,
        ]);

        TourActivity::updateOrCreate([
            'schedule_id' => $schedule5->id,
            'activity_id' => $activity2->id, // Using Snorkeling activity
            'is_active' => true,
        ]);

        FacadesDB::table('tour_category_mapping')->insert([
            ['tour_id' => $tour5->id, 'category_id' => $category1->id],
        ]);

        $tour6 = Tour::updateOrCreate(
            ['name' => 'Ancient Ruins Exploration'],
            [
                'description' => 'Explore the mysteries of ancient civilizations.',
                'short_description' => 'A journey through historical ruins.',
                'location_id' => 6,
                'duration_hours' => 7,
                'duration_days' => 1,
                'base_price' => 180.00,
                'discount_percentage' => 0,
                'max_capacity' => 20,
                'min_participants' => 5,
                'difficulty_level' => 3,
                'average_rating' => 4.7,
                'total_ratings' => 80,
                'is_active' => true,
                'is_featured' => true,
                'admin_id' => 6,
                'language' => 'en',
            ]
        );

        // Seed Tour Images for tour6 (same as tour1)
        TourImage::updateOrCreate([
            'tour_id' => $tour6->id,
            'image' => 'images/tour/1.jpg',
            'display_order' => 1,
        ]);
        TourImage::updateOrCreate([
            'tour_id' => $tour6->id,
            'image' => 'images/tour/2.jpg',
            'display_order' => 2,
        ]);
        TourImage::updateOrCreate([
            'tour_id' => $tour6->id,
            'image' => 'images/tour/3.jpg',
            'display_order' => 3,
        ]);

        $schedule6 = TourSchedule::updateOrCreate([
            'tour_id' => $tour6->id,
            'start_date' => '2025-11-20',
            'end_date' => '2025-11-20',
            'start_time' => '08:30:00',
            'available_spots' => 20,
            'price' => 180.00,
            'is_active' => true,
        ]);

        TourActivity::updateOrCreate([
            'schedule_id' => $schedule6->id,
            'activity_id' => $activity3->id, // Using City Tour activity
            'is_active' => true,
        ]);

        FacadesDB::table('tour_category_mapping')->insert([
            ['tour_id' => $tour6->id, 'category_id' => $category1->id],
        ]);
    }
}
