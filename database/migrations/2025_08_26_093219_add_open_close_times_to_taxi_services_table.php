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
        Schema::table('taxi_services', function (Blueprint $table) {
            $table->time('open_time')->nullable()->after('manager_id');
            $table->time('close_time')->nullable()->after('open_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('taxi_services', function (Blueprint $table) {
            $table->dropColumn(['open_time', 'close_time']);
        });
    }
};
