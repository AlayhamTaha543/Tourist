<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Restaurants chairs
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->double('discount')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('cuisine')->nullable();
            $table->enum('price_range', ['inexpensive', 'moderate', 'expensive', 'very_expensive'])->nullable();
            $table->float('price')->nullable();
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_ratings')->default(0);
            $table->string('main_image')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->integer('max_chairs')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_recommended')->default(false);
            $table->boolean('is_popular')->default(true);
            $table->unsignedBigInteger('admin_id')->constrained('admins', 'id')->cascadeOnDelete();
            $table->timestamps();
        });

        // Restaurant Images table
        Schema::create('restaurant_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('restaurants', 'id')->cascadeOnDelete();
            $table->string('image')->notNull();
            $table->integer('display_order')->default(0);
            $table->string('caption')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Restaurant Chairs table
        Schema::create('restaurant_chairs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('restaurants', 'id')->cascadeOnDelete();
            $table->integer('cost')->notNull();
            $table->enum('location', ['indoor', 'outdoor', 'private'])->nullable();
            $table->integer('total_chairs')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_reservable')->default(false);

            $table->timestamps();
        });
        Schema::create('chair_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_chair_id')->constrained('restaurant_chairs')->onDelete('cascade');
            $table->date('date');
            $table->time('time_slot');
            $table->integer('available_chairs_count')->default(0);
            $table->timestamps();

            // Ensure uniqueness for restaurant_chair_id, date, and time_slot combination
            $table->unique(['restaurant_chair_id', 'date', 'time_slot']);

            // Add indexes for better query performance
            $table->index(['date', 'time_slot']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_chairs');
        Schema::dropIfExists('restaurant_images');
        Schema::dropIfExists('restaurants');
        Schema::dropIfExists('chair_availabilities');
    }
};
