<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\RestaurantImage;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\RestaurantChair; // Import RestaurantChair
use App\Models\ChairAvailability; // Import ChairAvailability
use Carbon\Carbon; // Import Carbon
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Temporarily disable foreign key checks
        Schema::disableForeignKeyConstraints();

        // Truncate the table to start fresh
        ChairAvailability::truncate();

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();

        $restaurantsData = [
            [
                'name' => 'Al-Fakher Restaurant',
                'description' => 'A luxury restaurant offering a variety of dishes with excellent service.',
                'cuisine' => 'Saudi Cuisine',
                'main_image' => 'images/restaurant/r.png',
            ],
            [
                'name' => 'Golden Oasis',
                'description' => 'Traditional Saudi dishes with a modern twist.',
                'cuisine' => 'Middle Eastern',
                'main_image' => 'images/restaurant/r.png',
            ],
            [
                'name' => 'Palm Breeze',
                'description' => 'Seafood specialties with fresh ingredients.',
                'cuisine' => 'Seafood',
                'main_image' => 'images/restaurant/r.png',
            ],
            [
                'name' => 'Spice Souk',
                'description' => 'Indian and Arabic fusion cuisine with aromatic spices.',
                'cuisine' => 'Indian-Arabic Fusion',
                'main_image' => 'images/restaurant/r.png',
            ],
            [
                'name' => 'Desert Rose Diner',
                'description' => 'Casual dining with a focus on comfort food.',
                'cuisine' => 'International',
                'main_image' => 'images/restaurant/r.png',
            ],
        ];

        foreach ($restaurantsData as $index => $data) {
            $restaurant = Restaurant::updateOrCreate([
                'name' => $data['name'],
                'location_id' => 1, // Assuming a default location_id
            ], [
                'description' => $data['description'],
                'cuisine' => $data['cuisine'],
                'price_range' => rand(2, 4),
                'opening_time' => '10:00:00',
                'closing_time' => '23:00:00',
                'average_rating' => rand(35, 50) / 10,
                'total_ratings' => rand(50, 300),
                'main_image' => $data['main_image'],
                'website' => 'https://example.com/' . strtolower(str_replace(' ', '_', $data['name'])),
                'phone' => '+96612345678' . $index,
                'email' => 'info' . $index . '@restaurant.com',
                'max_chairs' => rand(20, 60),
                'price' => rand(80, 200),
                'is_active' => true,
                'is_featured' => (bool) rand(0, 1),
                'admin_id' => rand(1, 5), // Assuming admin_id exists
            ]);

            // Image
            RestaurantImage::updateOrCreate([
                'restaurant_id' => $restaurant->id,
                'display_order' => 1,
            ], [
                'image' => 'path/to/image_' . $index . '.jpg',
                'caption' => 'Photo of ' . $data['name'],
                'is_active' => true,
            ]);

            // Menu Category
            $category = MenuCategory::updateOrCreate([
                'restaurant_id' => $restaurant->id,
                'name' => 'Specialties',
            ], [
                'description' => 'Signature dishes of the restaurant',
                'display_order' => 1,
                'is_active' => true,
            ]);

            // Menu Item
            MenuItem::updateOrCreate([
                'category_id' => $category->id,
                'name' => 'Signature Dish ' . ($index + 1),
            ], [
                'description' => 'A special dish from ' . $data['name'],
                'price' => rand(20, 80),
                'is_vegetarian' => (bool) rand(0, 1),
                'is_vegan' => false,
                'is_gluten_free' => false,
                'spiciness' => 'mild',
                'image' => 'path/to/dish_' . $index . '.jpg',
                'is_active' => true,
                'is_featured' => false,
            ]);

            // Create Indoor Chairs
            $indoorChairs = RestaurantChair::updateOrCreate([
                'restaurant_id' => $restaurant->id,
                'location' => 'Indoor',
            ], [
                'total_chairs' => 20,
                'cost' => rand(50, 150),
                'is_active' => true,
                'is_reservable' => true,
            ]);

            // Create Outdoor Chairs
            $outdoorChairs = RestaurantChair::updateOrCreate([
                'restaurant_id' => $restaurant->id,
                'location' => 'Outdoor',
            ], [
                'total_chairs' => 30,
                'cost' => rand(50, 150),
                'is_active' => true,
                'is_reservable' => true,
            ]);

            // --- Add Chair Availability Seeding ---
            $chairTypes = [$indoorChairs, $outdoorChairs];

            // Generate availability for the next 7 days
            for ($dayOffset = 0; $dayOffset < 7; $dayOffset++) {
                $currentDate = Carbon::now()->addDays($dayOffset);
                $dateString = $currentDate->format('Y-m-d');

                // Generate time slots for the restaurant
                $openingTime = Carbon::parse($restaurant->opening_time);
                $closingTime = Carbon::parse($restaurant->closing_time);
                $timeSlots = [];
                $current = clone $openingTime;
                while ($current < $closingTime) {
                    $timeSlots[] = $current->format('H:i:s');
                    $current->addHour();
                }

                foreach ($chairTypes as $chairType) {
                    foreach ($timeSlots as $timeSlot) {
                        ChairAvailability::create([
                            'restaurant_chair_id' => $chairType->id,
                            'date' => $dateString,
                            'time_slot' => $timeSlot,
                            'available_chairs_count' => $chairType->total_chairs,
                        ]);
                    }
                }
            }
            // --- End Chair Availability Seeding ---
        }
    }
}
