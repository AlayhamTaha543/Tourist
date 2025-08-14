<?php

namespace Database\Seeders;

use App\Models\RentalVehicleCategory;
use Illuminate\Database\Seeder;

class RentalVehicleCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            // Standard categories for all offices
            ['name' => 'Economy', 'description' => 'Compact and fuel-efficient vehicles'],
            ['name' => 'Standard', 'description' => 'Mid-size comfortable vehicles'],
            ['name' => 'Premium', 'description' => 'Luxury and high-end vehicles'],

            // Additional categories (optional)
            ['name' => 'SUV', 'description' => 'Sport utility vehicles'],
            ['name' => 'Minivan', 'description' => 'Family-sized vehicles'],
            ['name' => 'Convertible', 'description' => 'Open-top vehicles'],
        ];

        foreach ($categories as $category) {
            RentalVehicleCategory::create($category);
        }
    }
}
