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
        // Travel Agencies table
        Schema::create('travel_agencies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete(); $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_ratings')->default(0);
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('admin_id')->nullable()->constrained('admins', 'id')->cascadeOnDelete();
            $table->timestamps();
        });

        // Travel Packages table
        Schema::create('travel_packages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agency_id')->constrained('travel_agencies', 'id');
            $table->string('name')->notNull();
            $table->text('description')->nullable();
            $table->integer('duration_days')->notNull();
            $table->decimal('base_price', 10, 2)->notNull();
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->integer('max_participants')->nullable();
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_ratings')->default(0);
            $table->string('main_image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        // Package Destinations table
        Schema::create('package_destinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('travel_packages', 'id');
            $table->foreignId('location_id')->constrained('locations', 'id');
            $table->integer('day_number')->notNull();
            $table->text('description')->nullable();
            $table->string('duration')->nullable();
            $table->timestamps();
        });

        // Package Inclusions table
        Schema::create('package_inclusions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('travel_packages', 'id');
            $table->enum('inclusion_type', ['tour', 'hotel', 'transport', 'meal', 'other'])->notNull();
            $table->string('description')->notNull();
            $table->boolean('is_highlighted')->default(false);
            $table->timestamps();
        });

        Schema::create('travel_flights', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agency_id')->constrained('travel_agencies','id')->onDelete('cascade');
            $table->string('flight_number')->unique();
            $table->unsignedBigInteger('departure_id')->constrained('locations')->onDelete('cascade');
            $table->unsignedBigInteger('arrival_id')->constrained('locations')->onDelete('cascade');
            $table->dateTime('departure_time');
            $table->dateTime('arrival_time');
            $table->integer('duration_minutes')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('available_seats');
            $table->enum('status', ['scheduled', 'delayed', 'cancelled'])->default('scheduled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_inclusions');
        Schema::dropIfExists('package_destinations');
        Schema::dropIfExists('travel_packages');
        Schema::dropIfExists('travel_agencies');
        Schema::dropIfExists('travel_flights');
    }
};
