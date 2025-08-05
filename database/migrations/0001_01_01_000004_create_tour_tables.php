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
        // Tour Categories table
        Schema::create('tour_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->notNull();
            $table->text('description')->nullable();
            $table->foreignId('parent_category_id')->nullable()->constrained('tour_categories', 'id')->cascadeOnDelete();
            $table->string('icon')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tours table
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('short_description')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->decimal('duration_hours', 5, 2)->nullable();
            $table->integer('duration_days')->nullable();
            $table->decimal('base_price', 10, 2)->notNull();
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->integer('max_capacity')->notNull();
            $table->integer('min_participants')->default(1);
            $table->enum('difficulty_level', ['easy', 'moderate', 'difficult'])->default('easy');
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_ratings')->default(0);
            $table->string('main_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedBigInteger('admin_id')->constrained('admins', 'id')->cascadeOnDelete();
            $table->timestamps();
        });

        // Tour Images table
        Schema::create('tour_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours', 'id');
            $table->string('image')->notNull();
            $table->integer('display_order')->default(0);
            $table->string('caption')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Tour Category Mapping table
        Schema::create('tour_category_mapping', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours', 'id');
            $table->foreignId('category_id')->constrained('tour_categories', 'id');
            $table->timestamps();
        });

        // Tour Schedules table
        Schema::create('tour_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours', 'id');
            $table->date('start_date')->notNull();
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->integer('available_spots')->notNull();
            $table->decimal('price', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->notNull();
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
        Schema::create('tour_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('tour_schedules', 'id');
            $table->foreignId('activity_id')->constrained('activities', 'id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_schedules');
        Schema::dropIfExists('tour_category_mapping');
        Schema::dropIfExists('tour_images');
        Schema::dropIfExists('tours');
        Schema::dropIfExists('tour_crategories');
    }
};
