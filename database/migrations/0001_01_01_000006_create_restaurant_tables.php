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
        // Restaurants table
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
            $table->integer('max_tables')->nullable();
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
            $table->foreignId('restaurant_id')->constrained('restaurants', 'id');
            $table->string('image')->notNull();
            $table->integer('display_order')->default(0);
            $table->string('caption')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Menu Categories table
        Schema::create('menu_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('restaurants', 'id');
            $table->string('name')->notNull();
            $table->text('description')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Menu Items table
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('menu_categories', 'id');
            $table->string('name')->notNull();
            $table->text('description')->nullable();
            $table->decimal('price', 10)->notNull();
            $table->json('sizes')->nullable();
            $table->boolean('is_vegetarian')->default(false);
            $table->boolean('is_vegan')->default(false);
            $table->boolean('is_gluten_free')->default(false);
            $table->enum('spiciness', ['not_spicy', 'mild', 'medium', 'hot'])->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        // Restaurant Tables table
        Schema::create('restaurant_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('restaurants', 'id');
            $table->string('number')->notNull();
            $table->integer('cost')->notNull();
            $table->enum('location', ['indoor', 'outdoor', 'private'])->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('table_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id')->constrained('restaurant_tables')->onDelete('cascade');
            $table->date('date');
            $table->time('time_slot');
            $table->boolean('is_available')->default(true);
            $table->boolean('is_blocked')->default(false);
            $table->decimal('price_multiplier', 3, 2)->default(1.00);
            $table->timestamps();
            
            // Ensure uniqueness for table, date, and time slot combination
            $table->unique(['table_id', 'date', 'time_slot']);
            
            // Add indexes for better query performance
            $table->index(['date', 'time_slot']);
            $table->index(['is_available', 'is_blocked']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_tables');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menu_categories');
        Schema::dropIfExists('restaurant_images');
        Schema::dropIfExists('restaurants');
        Schema::dropIfExists('table_availabilities');
    }
};
