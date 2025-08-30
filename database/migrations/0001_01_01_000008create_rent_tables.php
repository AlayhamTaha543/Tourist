<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rental_offices', function (Blueprint $table) {
            $table->id('id');
            $table->string('name', 100);
            $table->string('address', 255)->nullable();
            $table->double('rating')->default(2.5);
            $table->string('image', 50)->nullable();

            $table->unsignedBigInteger('location_id');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');

            $table->unsignedBigInteger('manager_id');
            $table->foreign('manager_id')->references('id')->on('admins')->onDelete('cascade');

            $table->timestamps();
        });

        Schema::create('rental_vehicle_categories', function (Blueprint $table) {
            $table->id('id');
            $table->string('name', 50);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('rental_vehicles', function (Blueprint $table) {
            $table->id('id');

            $table->unsignedBigInteger('office_id');
            // Add onDelete('cascade') to automatically delete vehicles when office is deleted
            $table->foreign('office_id')->references('id')->on('rental_offices')->onDelete('cascade');

            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('rental_vehicle_categories')->onDelete('cascade');
            $table->decimal('price_per_day', 8, 2);

            $table->string('license_plate', 20)->unique();
            $table->string('make', 50)->nullable();
            $table->string('model', 50)->nullable();
            $table->string('image', 50)->nullable();
            $table->year('year')->nullable();
            $table->unsignedTinyInteger('seating_capacity')->nullable();

            $table->enum('status', ['available', 'reserved', 'in_maintenance'])
                ->default('available');

            $table->timestamps();
        });

        Schema::create('rental_vehicle_status_history', function (Blueprint $table) {
            $table->id('id');

            $table->unsignedBigInteger('vehicle_id');
            // Add onDelete('cascade') to automatically delete history when vehicle is deleted
            $table->foreign('vehicle_id')->references('id')->on('rental_vehicles')->onDelete('cascade');

            $table->enum('old_status', ['available', 'reserved', 'in_maintenance']);
            $table->enum('new_status', ['available', 'reserved', 'in_maintenance']);

            $table->unsignedBigInteger('changed_by_id');
            $table->foreign('changed_by_id')->references('id')->on('admins')->onDelete('cascade');

            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_vehicle_status_history');
        Schema::dropIfExists('rental_vehicles');
        Schema::dropIfExists('rental_vehicle_categories');
        Schema::dropIfExists('rental_offices');
    }
};
