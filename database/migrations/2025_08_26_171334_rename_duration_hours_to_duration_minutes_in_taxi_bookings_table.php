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
        Schema::table('taxi_bookings', function (Blueprint $table) {
            $table->renameColumn('duration_hours', 'duration_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('taxi_bookings', function (Blueprint $table) {
            $table->renameColumn('duration_minutes', 'duration_hours');
        });
    }
};
