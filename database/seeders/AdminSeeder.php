<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::updateOrCreate(['email' => 'alayham@gmail.com'], [
            'name' => 'alayham',
            'password' => bcrypt('11111111'),
            'role' => 'super_admin',
        ]);
        Admin::updateOrCreate(['email' => 'alayham2@gmail.com'], [
            'name' => 'alayham2',
            'password' => bcrypt('11111111'),
            'role' => 'admin',
            'section' => 'restaurant'
        ]);
        Admin::updateOrCreate(['email' => 'alayham3@gmail.com'], [
            'name' => 'alayham3',
            'password' => bcrypt('11111111'),
            'role' => 'sub_admin',
            'section' => 'restaurant'
        ]);
        Admin::updateOrCreate(['email' => 'alayham4@gmail.com'], [
            'name' => 'alayham4',
            'password' => bcrypt('11111111'),
            'role' => 'admin',
            'section' => 'hotel'
        ]);
        Admin::updateOrCreate(['email' => 'alayham5@gmail.com'], [
            'name' => 'alayham5',
            'password' => bcrypt('11111111'),
            'role' => 'sub_admin',
            'section' => 'hotel'
        ]);
        Admin::updateOrCreate(['email' => 'alayham6@gmail.com'], [
            'name' => 'alayham6',
            'password' => bcrypt('11111111'),
            'role' => 'admin',
            'section' => 'tour'
        ]);
        Admin::updateOrCreate(['email' => 'alayham7@gmail.com'], [
            'name' => 'alayham7',
            'password' => bcrypt('11111111'),
            'role' => 'sub_admin',
            'section' => 'tour'
        ]);
        Admin::updateOrCreate(['email' => 'alayham8@gmail.com'], [
            'name' => 'alayham8',
            'password' => bcrypt('11111111'),
            'role' => 'admin',
            'section' => 'travel'
        ]);
        Admin::updateOrCreate(['email' => 'alayham9@gmail.com'], [
            'name' => 'alayham9',
            'password' => bcrypt('11111111'),
            'role' => 'sub_admin',
            'section' => 'travel'
        ]);
    }
}