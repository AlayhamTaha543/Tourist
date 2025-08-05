<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hotels table
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->double('discount')->nullable();
            $table->integer('star_rating')->nullable();
            $table->time('checkIn_time')->nullable();
            $table->time('checkOut_time')->nullable();
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_ratings')->default(0);
            $table->string('main_image')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedBigInteger('admin_id')->constrained('admins', 'id')->cascadeOnDelete();
            $table->timestamps();
        });

        // Hotel Images table
        Schema::create('hotel_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels', 'id');
            $table->string('image')->notNull();
            $table->integer('display_order')->default(0);
            $table->string('caption')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Hotel Amenities table
        Schema::create('hotel_amenities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->notNull();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Hotel Amenity Mapping table
        Schema::create('hotel_amenity_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels', 'id');
            $table->foreignId('amenity_id')->constrained('hotel_amenities', 'id');
            $table->unique(['hotel_id', 'amenity_id']);
            $table->timestamps();
        });

        // Room Types table
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels', 'id');
            $table->string('name')->notNull();
            $table->integer('number');
            $table->text('description')->nullable();
            $table->integer('max_occupancy')->notNull();
            $table->decimal('base_price', 10, 2)->notNull();
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->string('size')->nullable();
            $table->string('bed_type')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Room Availability table
        Schema::create('room_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained('room_types', 'id');
            $table->date('date')->notNull();
            $table->integer('available_rooms')->notNull();
            $table->decimal('price', 10, 2)->nullable();
            $table->boolean('is_blocked')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_availabilities');
        Schema::dropIfExists('room_types');
        Schema::dropIfExists('hotel_amenity_maps');
        Schema::dropIfExists('hotel_amenities');
        Schema::dropIfExists('hotel_images');
        Schema::dropIfExists('hotels');
    }
};
