<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\RestaurantImage;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\RestaurantTable;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    public function run()
    {
        $restaurantsData = [
            [
                'name' => 'Al-Fakher Restaurant',
                'description' => 'A luxury restaurant offering a variety of dishes with excellent service.',
                'cuisine' => 'Saudi Cuisine',
                'main_image' => 'path/to/alfakher.jpg',
            ],
            [
                'name' => 'Golden Oasis',
                'description' => 'Traditional Saudi dishes with a modern twist.',
                'cuisine' => 'Middle Eastern',
                'main_image' => 'path/to/golden_oasis.jpg',
            ],
            [
                'name' => 'Palm Breeze',
                'description' => 'Seafood specialties with fresh ingredients.',
                'cuisine' => 'Seafood',
                'main_image' => 'path/to/palm_breeze.jpg',
            ],
            [
                'name' => 'Spice Souk',
                'description' => 'Indian and Arabic fusion cuisine with aromatic spices.',
                'cuisine' => 'Indian-Arabic Fusion',
                'main_image' => 'path/to/spice_souk.jpg',
            ],
            [
                'name' => 'Desert Rose Diner',
                'description' => 'Casual dining with a focus on comfort food.',
                'cuisine' => 'International',
                'main_image' => 'path/to/desert_rose.jpg',
            ],
        ];

        foreach ($restaurantsData as $index => $data) {
            $restaurant = Restaurant::updateOrCreate([
                'name' => $data['name'],
                'location_id' => 1,
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
                'max_tables' => rand(20, 60),
                'price' => rand(80, 200),
                'is_active' => true,
                'is_featured' => (bool) rand(0, 1),
                'admin_id' => rand(1, 5),
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

            // Table
            RestaurantTable::updateOrCreate([
                'restaurant_id' => $restaurant->id,
                'number' => 'T' . ($index + 1),
            ], [
                'cost' => rand(50, 150),
                'location' => 'Indoor',
                'is_active' => true,
            ]);
        }
    }
}