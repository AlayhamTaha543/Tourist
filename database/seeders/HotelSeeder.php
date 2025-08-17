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

        // 1. Grand Palace Hotel (your original)
        $this->createHotel(
            'Grand Palace Hotel',
            'A luxurious hotel in the heart of the city.',
            1,
            10,
            5,
            '14:00:00',
            '12:00:00',
            4.7,
            124,
            'images/hotels/1.png',
            'https://grandpalace.com',
            '+1 234 567 8900',
            'contact@grandpalace.com',
            true,
            true,
            5,
            [
                ['name' => 'Free Wi-Fi', 'icon' => 'icons/wifi.png'],
                ['name' => 'Swimming Pool', 'icon' => 'icons/pool.png'],
                ['name' => 'Gym', 'icon' => 'icons/gym.png'],
                ['name' => 'Spa', 'icon' => 'icons/spa.png'],
                ['name' => 'Airport Shuttle', 'icon' => 'icons/shuttle.png'],
            ],
            [
                [
                    'name' => 'Deluxe King Room',
                    'description' => 'Spacious room with king-sized bed.',
                    'max_occupancy' => 2,
                    'base_price' => 200.00,
                    'discount' => 15.00,
                    'size' => '35 sqm',
                    'bed_type' => 'King',
                    'image' => 'images/rooms/deluxe-king.jpg',
                    'available_rooms' => 10,
                    'price' => 180.00
                ]
            ]
        );

        // 2. Ocean View Resort
        $this->createHotel(
            'Ocean View Resort',
            'Beachfront resort with stunning ocean views.',
            1,
            15,
            4,
            '15:00:00',
            '11:00:00',
            4.5,
            89,
            'images/hotels/1.png',
            'https://oceanviewresort.com',
            '+1 345 678 9012',
            'info@oceanviewresort.com',
            true,
            true,
            5,
            [
                ['name' => 'Private Beach', 'icon' => 'icons/beach.png'],
                ['name' => 'Restaurant', 'icon' => 'icons/restaurant.png'],
                ['name' => 'Free Wi-Fi', 'icon' => 'icons/wifi.png'],
                ['name' => 'Swimming Pool', 'icon' => 'icons/pool.png'],
                ['name' => 'Water Sports', 'icon' => 'icons/watersports.png'],
            ],
            [
                [
                    'name' => 'Ocean View Suite',
                    'description' => 'Luxury suite with balcony overlooking the ocean.',
                    'max_occupancy' => 4,
                    'base_price' => 350.00,
                    'discount' => 10.00,
                    'size' => '50 sqm',
                    'bed_type' => 'King + Sofa Bed',
                    'image' => 'images/rooms/ocean-suite.jpg',
                    'available_rooms' => 8,
                    'price' => 315.00
                ],
                [
                    'name' => 'Standard Room',
                    'description' => 'Comfortable room with partial ocean view.',
                    'max_occupancy' => 2,
                    'base_price' => 180.00,
                    'discount' => 5.00,
                    'size' => '28 sqm',
                    'bed_type' => 'Queen',
                    'image' => 'images/rooms/standard.jpg',
                    'available_rooms' => 15,
                    'price' => 171.00
                ]
            ]
        );

        // 3. Mountain Retreat Lodge
        $this->createHotel(
            'Mountain Retreat Lodge',
            'Cozy lodge nestled in the mountains with nature views.',
            1,
            5,
            3,
            '16:00:00',
            '10:00:00',
            4.2,
            56,
            'images/hotels/1.png',
            'https://mountainretreat.com',
            '+1 456 789 0123',
            'stay@mountainretreat.com',
            true,
            false,
            5,
            [
                ['name' => 'Fireplace', 'icon' => 'icons/fireplace.png'],
                ['name' => 'Hiking Trails', 'icon' => 'icons/hiking.png'],
                ['name' => 'Hot Tub', 'icon' => 'icons/hottub.png'],
                ['name' => 'Free Parking', 'icon' => 'icons/parking.png'],
                ['name' => 'Pet Friendly', 'icon' => 'icons/pet.png'],
            ],
            [
                [
                    'name' => 'Mountain View Cabin',
                    'description' => 'Rustic cabin with panoramic mountain views.',
                    'max_occupancy' => 4,
                    'base_price' => 220.00,
                    'discount' => 0.00,
                    'size' => '40 sqm',
                    'bed_type' => 'Queen + Bunk',
                    'image' => 'images/rooms/mountain-cabin.jpg',
                    'available_rooms' => 5,
                    'price' => 220.00
                ],
                [
                    'name' => 'Standard Room',
                    'description' => 'Cozy room with forest view.',
                    'max_occupancy' => 2,
                    'base_price' => 120.00,
                    'discount' => 10.00,
                    'size' => '25 sqm',
                    'bed_type' => 'Queen',
                    'image' => 'images/rooms/standard-lodge.jpg',
                    'available_rooms' => 10,
                    'price' => 108.00
                ]
            ]
        );

        // 4. Urban Boutique Hotel
        $this->createHotel(
            'Urban Boutique Hotel',
            'Trendy hotel in the downtown district with modern design.',
            4,
            20,
            4,
            '14:00:00',
            '12:00:00',
            4.3,
            112,
            'images/hotels/1.png',
            'https://urbanboutique.com',
            '+1 567 890 1234',
            'hello@urbanboutique.com',
            true,
            true,
            5,
            [
                ['name' => 'Rooftop Bar', 'icon' => 'icons/bar.png'],
                ['name' => 'Free Wi-Fi', 'icon' => 'icons/wifi.png'],
                ['name' => '24/7 Room Service', 'icon' => 'icons/roomservice.png'],
                ['name' => 'Business Center', 'icon' => 'icons/business.png'],
                ['name' => 'Fitness Center', 'icon' => 'icons/gym.png'],
            ],
            [
                [
                    'name' => 'Executive Suite',
                    'description' => 'Stylish suite with city views and workspace.',
                    'max_occupancy' => 2,
                    'base_price' => 280.00,
                    'discount' => 15.00,
                    'size' => '45 sqm',
                    'bed_type' => 'King',
                    'image' => 'images/rooms/executive.jpg',
                    'available_rooms' => 6,
                    'price' => 238.00
                ],
                [
                    'name' => 'Standard Double',
                    'description' => 'Modern room with all essential amenities.',
                    'max_occupancy' => 2,
                    'base_price' => 160.00,
                    'discount' => 10.00,
                    'size' => '30 sqm',
                    'bed_type' => 'Queen',
                    'image' => 'images/rooms/standard-double.jpg',
                    'available_rooms' => 12,
                    'price' => 144.00
                ]
            ]
        );

        // 5. Heritage Grand Hotel
        $this->createHotel(
            'Heritage Grand Hotel',
            'Historic luxury hotel with classic architecture and modern comforts.',
            5,
            0,
            5,
            '15:00:00',
            '12:00:00',
            4.8,
            215,
            'images/hotels/1.png',
            'https://heritagegrand.com',
            '+1 678 901 2345',
            'reservations@heritagegrand.com',
            true,
            true,
            5,
            [
                ['name' => 'Historic Building', 'icon' => 'icons/historic.png'],
                ['name' => 'Fine Dining', 'icon' => 'icons/dining.png'],
                ['name' => 'Concierge', 'icon' => 'icons/concierge.png'],
                ['name' => 'Spa', 'icon' => 'icons/spa.png'],
                ['name' => 'Valet Parking', 'icon' => 'icons/valet.png'],
            ],
            [
                [
                    'name' => 'Presidential Suite',
                    'description' => 'Opulent suite with separate living area and butler service.',
                    'max_occupancy' => 4,
                    'base_price' => 600.00,
                    'discount' => 0.00,
                    'size' => '80 sqm',
                    'bed_type' => 'King + Twin',
                    'image' => 'images/rooms/presidential.jpg',
                    'available_rooms' => 2,
                    'price' => 600.00
                ],
                [
                    'name' => 'Deluxe Room',
                    'description' => 'Elegant room with period furnishings.',
                    'max_occupancy' => 2,
                    'base_price' => 300.00,
                    'discount' => 10.00,
                    'size' => '40 sqm',
                    'bed_type' => 'King',
                    'image' => 'images/rooms/deluxe-heritage.jpg',
                    'available_rooms' => 15,
                    'price' => 270.00
                ]
            ]
        );
    }

    protected function createHotel(
        $name,
        $description,
        $location_id,
        $discount,
        $star_rating,
        $checkIn_time,
        $checkOut_time,
        $average_rating,
        $total_ratings,
        $main_image,
        $website,
        $phone,
        $email,
        $is_active,
        $is_featured,
        $admin_id,
        $amenities,
        $roomTypes
    ) {
        $hotel = Hotel::updateOrCreate([
            'name' => $name,
            'description' => $description,
            'location_id' => $location_id,
            'discount' => $discount,
            'star_rating' => $star_rating,
            'checkIn_time' => $checkIn_time,
            'checkOut_time' => $checkOut_time,
            'average_rating' => $average_rating,
            'total_ratings' => $total_ratings,
            'main_image' => $main_image,
            'website' => $website,
            'phone' => $phone,
            'email' => $email,
            'is_active' => $is_active,
            'is_featured' => $is_featured,
            'admin_id' => $admin_id,
        ]);

        foreach ($roomTypes as $roomTypeData) {
            $roomType = RoomType::updateOrCreate([
                'hotel_id' => $hotel->id,
                'name' => $roomTypeData['name'],
                'number' => $roomTypeData['available_rooms'],
                'description' => $roomTypeData['description'],
                'max_occupancy' => $roomTypeData['max_occupancy'],
                'base_price' => $roomTypeData['base_price'],
                'discount_percentage' => $roomTypeData['discount'],
                'size' => $roomTypeData['size'],
                'bed_type' => $roomTypeData['bed_type'],
                'image' => $roomTypeData['image'],
                'is_active' => true,
            ]);

            foreach (range(0, 6) as $offset) {
                RoomAvailability::updateOrCreate([
                    'room_type_id' => $roomType->id,
                    'date' => now()->addDays($offset)->toDateString(),
                    'available_rooms' => $roomTypeData['available_rooms'],
                    'price' => $roomTypeData['price'],
                    'is_blocked' => false,
                ]);
            }
        }

        foreach ($amenities as $item) {
            $amenity = HotelAmenity::updateOrCreate([
                'name' => $item['name'],
                'icon' => $item['icon'],
                'is_active' => true,
            ]);

            HotelAmenityMap::updateOrCreate([
                'hotel_id' => $hotel->id,
                'amenity_id' => $amenity->id,
            ]);
        }
    }
}
