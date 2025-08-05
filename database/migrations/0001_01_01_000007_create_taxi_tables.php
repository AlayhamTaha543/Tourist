<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // taxi_services table
        Schema::create('taxi_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_ratings')->default(0);
            $table->string('logo_url')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // vehicle_types table
        Schema::create('vehicle_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taxi_service_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('max_passengers');
            $table->decimal('price_per_km', 10, 2);
            $table->decimal('base_price', 10, 2);
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
        });

        // vehicles table
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taxi_service_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_type_id')->constrained()->onDelete('cascade');
            $table->string('registration_number');
            $table->string('model')->nullable();
            $table->integer('year')->nullable();
            $table->string('color')->nullable();
            $table->boolean('is_active')->default(true);
        });

        // drivers table
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained()->onDelete('cascade');
            $table->foreignId('taxi_service_id')->constrained()->onDelete('cascade');
            $table->string('license_number');
            $table->integer('experience_years')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->unsignedInteger('rating_count')->default(0);
            $table->timestamp('rating_updated_at')->nullable();
            $table->enum('availability_status', ['available', 'busy', 'offline'])->default('offline');
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('location_updated_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->time('shift_start')->nullable();
            $table->time('shift_end')->nullable();
            $table->timestamps();

            // Regular index for status
            $table->index('availability_status', 'drivers_availability_status_idx');
        });

        DB::statement('ALTER TABLE drivers ADD current_location POINT NOT NULL');
        DB::statement('CREATE SPATIAL INDEX current_location_index ON drivers(current_location)');


        // trips table
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'accepted', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamp('requested_at');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->decimal('fare', 10, 2)->nullable();
            $table->decimal('distance_km', 10, 2)->nullable();
            $table->decimal('surge_multiplier', 5, 2)->default(1.00);
            $table->enum('trip_type', ['solo', 'pool'])->default('solo');
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->onDelete('set null');
            $table->timestamps();
        });

        DB::statement('ALTER TABLE trips ADD pickup_location POINT NOT NULL');
        DB::statement('ALTER TABLE trips ADD dropoff_location POINT NOT NULL');
        DB::statement('CREATE SPATIAL INDEX pickup_location_index ON trips(pickup_location)');
        DB::statement('CREATE SPATIAL INDEX dropoff_location_index ON trips(dropoff_location)');
        DB::statement('CREATE INDEX status_index ON trips(status)');


        //pivot table for linking the driver with the vehicle he takes
        Schema::create('driver_vehicle_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->timestamp('assigned_at')->useCurrent(); // When the driver takes the vehicle
            $table->timestamp('unassigned_at')->nullable(); // When the driver returns the vehicle
            $table->unique(['driver_id', 'vehicle_id'], 'active_assignment_unique')
                ->whereNull('unassigned_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
        Schema::dropIfExists('drivers');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('vehicle_types');
        Schema::dropIfExists('taxi_services');
        Schema::dropIfExists('driver_vehicle_assignments');
    }
};
