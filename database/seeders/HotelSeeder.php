<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\HotelAmenity;
use App\Models\HotelAmenityMap;
use App\Models\RoomAvailability;
use App\Models\RoomType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hotel = Hotel::create([
            'name' => 'Grand Palace Hotel',
            'description' => 'A luxurious hotel in the heart of the city.',
            'location_id'=>1,
            'discount' => 10,
            'star_rating' => 5,
            'checkIn_time' => '14:00:00',
            'checkOut_time' => '12:00:00',
            'average_rating' => 4.7,
            'total_ratings' => 124,
            'main_image' => 'images/hotels/grand-palace.jpg',
            'website' => 'https://grandpalace.com',
            'phone' => '+1 234 567 8900',
            'email' => 'contact@grandpalace.com',
            'is_active' => true,
            'is_featured' => true,
            'admin_id' => 5,
        ]);

        $roomType = RoomType::create([
            'hotel_id' => $hotel->id,
            'name' => 'Deluxe King Room',
            'number' => 20,
            'description' => 'Spacious room with king-sized bed.',
            'max_occupancy' => 2,
            'base_price' => 200.00,
            'discount_percentage' => 15.00,
            'size' => '35 sqm',
            'bed_type' => 'King',
            'image' => 'images/rooms/deluxe-king.jpg',
            'is_active' => true,
        ]);

        foreach (range(0, 6) as $offset) {
            RoomAvailability::create([
                'roomType_id' => $roomType->id,
                'date' => now()->addDays($offset)->toDateString(),
                'available_rooms' => 10,
                'price' => 180.00,
                'is_blocked' => false,
            ]);
        }

        $amenities = [
            ['name' => 'Free Wi-Fi', 'icon' => 'icons/wifi.png'],
            ['name' => 'Swimming Pool', 'icon' => 'icons/pool.png'],
            ['name' => 'Gym', 'icon' => 'icons/gym.png'],
            ['name' => 'Spa', 'icon' => 'icons/spa.png'],
            ['name' => 'Airport Shuttle', 'icon' => 'icons/shuttle.png'],
        ];

        foreach ($amenities as $item) {
            $amenity = HotelAmenity::create([
                'name' => $item['name'],
                'icon' => $item['icon'],
                'is_active' => true,
            ]);

            HotelAmenityMap::create([
                'hotel_id' => $hotel->id,
                'amenity_id' => $amenity->id,
            ]);
        }
    }
}
