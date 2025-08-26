<?php

namespace Database\Seeders;

use App\Models\DiscountPoint;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DiscountPointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DiscountPoint::firstOrCreate(
            ['action' => 'book_flight', 'min_points' => 20000],
            ['required_points' => 20000, 'discount_percentage' => 75]
        );

        DiscountPoint::firstOrCreate(
            ['action' => 'book_flight', 'min_points' => 10000],
            ['required_points' => 10000, 'discount_percentage' => 50]
        );

        DiscountPoint::firstOrCreate(
            ['action' => 'book_flight', 'min_points' => 5000],
            ['required_points' => 5000, 'discount_percentage' => 10]
        );
    }
}