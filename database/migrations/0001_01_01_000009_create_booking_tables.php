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
        // Bookings table
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference')->unique()->notNull();
            $table->foreignId('user_id')->constrained('users', 'id');
            $table->enum('booking_type', ['tour', 'hotel', 'taxi', 'restaurant', 'package'])->notNull();
            $table->dateTime('booking_date')->default(now());
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->decimal('total_price', 10, 2)->notNull();
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->enum('payment_status', ['pending', 'paid', 'refunded', 'failed'])->default('pending');
            $table->text('special_requests')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
        });
        Schema::create('travel_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id')->constrained('Bookings', 'id');
            $table->unsignedBigInteger('user_id')->constrained('user','id')->onDelete('cascade');
            $table->unsignedBigInteger('flight_id')->constrained('travel_flights','id')->onDelete('cascade');
            $table->enum('ticket_type', ['one_way', 'round_trip'])->default('one_way');
            $table->integer('number_of_people')->default(1);
            $table->date('booking_date');
            $table->double('total_price');
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
        // Tour Bookings table
        Schema::create('tour_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings', 'id');
            $table->foreignId('tour_id')->constrained('tours', 'id');
            $table->foreignId('schedule_id')->constrained('tour_schedules', 'id');
            $table->integer('number_of_adults')->notNull()->default(1);
            $table->integer('number_of_children')->notNull()->default(0);
            $table->double('cost')->notNull();
            $table->timestamps();
        });

        // Hotel Bookings table
        Schema::create('hotel_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings', 'id');
            $table->foreignId('user_id')->constrained('users', 'id');
            $table->foreignId('hotel_id')->constrained('hotels', 'id');
            $table->foreignId('room_type_id')->constrained('room_types', 'id');
            $table->integer('hotel_room');
            $table->date('check_in_date')->notNull();
            $table->date('check_out_date')->notNull();
            $table->integer('number_of_rooms')->notNull()->default(1);
            $table->integer('number_of_guests')->notNull();
            $table->double('cost')->notNull();
            $table->timestamps();

        });

        // Restaurant Bookings table
        Schema::create('restaurant_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings', 'id');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('restaurant_id')->constrained('restaurants');
            $table->integer('table_id');
            $table->json('order')->nullable();
            $table->date('reservation_date');
            $table->time('reservation_time');
            $table->integer('number_of_guests');
            $table->string('location')->nullable();
            $table->decimal('cost')->nullable();
            $table->timestamps();
        });

        // Taxi Bookings table
        Schema::create('taxi_bookings', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('booking_id')->constrained('bookings', 'id')->onDelete('cascade');
            $table->foreignId('taxi_service_id')->constrained('taxi_services', 'id');
            $table->foreignId('vehicle_type_id')->constrained('vehicle_types', 'id');
            $table->foreignId('trip_id')->nullable()->constrained();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles', 'id');
            $table->foreignId('driver_id')->references('id')->on('drivers')->onDelete('cascade');
            $table->foreignId('pickup_location_id')->nullable()->constrained('locations', 'id');
            $table->foreignId('dropoff_location_id')->nullable()->constrained('locations', 'id');
            $table->dateTime('pickup_date_time')->notNull();
            $table->enum('type_of_booking', ['one_way', 'round_trip', 'hourly'])->default('one_way');
            $table->decimal('estimated_distance', 10, 2)->nullable();
            $table->integer('duration_hours')->nullable();       // for hourly
            $table->dateTime('return_time')->nullable();         // for round-trip
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->boolean('is_scheduled')->default(false);
            $table->boolean('is_shared')->default(false);        // for shared taxi booking
            $table->integer('passenger_count')->default(1);      // number of passengers in this booking
            $table->integer('max_additional_passengers')->nullable(); // max additional passengers allowed for shared rides
        });

        // Package Bookings table
        Schema::create('package_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('travel_packages', 'id');
            $table->integer('number_of_adults')->notNull()->default(1);
            $table->integer('number_of_children')->notNull()->default(0);
            $table->decimal('cost', 10, 2);
            $table->timestamps();
        });
        Schema::create('rental_bookings', function (Blueprint $table) {
            $table->foreignId('booking_id')->primary()->constrained('bookings')->onDelete('cascade');

            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('users');

            // Changed from unsignedInteger to unsignedBigInteger to match rental_vehicles.id
            $table->unsignedBigInteger('vehicle_id');
            $table->foreign('vehicle_id')->references('id')->on('rental_vehicles');

            $table->unsignedBigInteger('office_id');
            $table->foreign('office_id')->references('id')->on('rental_offices');

            $table->date('pickup_date');
            $table->date('return_date');
            $table->decimal('daily_rate', 8, 2);
            $table->decimal('total_price', 10, 2);

            $table->enum('status', ['reserved', 'active', 'completed', 'cancelled'])
                ->default('reserved');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_bookings');
        Schema::dropIfExists('rental_bookings');
        Schema::dropIfExists('package_bookings');
        Schema::dropIfExists('taxi_bookings');
        Schema::dropIfExists('restaurant_bookings');
        Schema::dropIfExists('hotel_bookings');
        Schema::dropIfExists('tour_bookings');
        Schema::dropIfExists('bookings');
    }
};
