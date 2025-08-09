<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\RestaurantImage;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\RestaurantTable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RestaurantSeeder extends Seeder
{
    public function run()
    {
        $restaurant = Restaurant::create([
            'name' => 'Al-Fakher Restaurant',
            'description' => 'A luxury restaurant offering a variety of dishes with excellent service.',
            'location_id' => 1, 
            'cuisine' => 'Saudi Cuisine',
            'price_range' => 3,
            'opening_time' => '10:00:00',
            'closing_time' => '23:00:00',
            'average_rating' => 4.5,
            'total_ratings' => 150,
            'main_image' => 'path/to/image.jpg',
            'website' => 'https://restaurant-website.com',
            'phone' => '+966123456789',
            'email' => 'info@restaurant.com',
            'max_tables' => 50,
            'cost' => 100.00,
            'is_active' => true,
            'is_featured' => true,
            'admin_id' => 3, 
        ]);

        RestaurantImage::create([
            'restaurant_id' => $restaurant->id,
            'image' => 'path/to/restaurant_image_1.jpg',
            'display_order' => 1,
            'caption' => 'Exterior view of the restaurant',
            'is_active' => true,
        ]);

        $category = MenuCategory::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Appetizers',
            'description' => 'Various appetizers',
            'display_order' => 1,
            'is_active' => true,
        ]);

        MenuItem::create([
            'category_id' => $category->id,
            'name' => 'Mutabal',
            'description' => 'Fresh appetizers with tahini and olive oil.',
            'price' => 25.00,
            'is_vegetarian' => true,
            'is_vegan' => true,
            'is_gluten_free' => false,
            'spiciness' => 'mild',
            'image' => 'path/to/item_image_1.jpg',
            'is_active' => true,
            'is_featured' => false,
        ]);

        RestaurantTable::create([
            'restaurant_id' => $restaurant->id,
            'number' => 'T1',
            'cost' => 100,
            'location' => 'Indoor',
            'is_active' => true,
        ]);
    }
}
