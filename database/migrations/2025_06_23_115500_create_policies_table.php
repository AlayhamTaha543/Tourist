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
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->enum('service_type', ['hotel', 'restaurant', 'flight', 'tour']);
            $table->unsignedBigInteger('service_id')->nullable();
            $table->enum('policy_type', ['cancellation', 'modification']);
            $table->text('description')->nullable();
            $table->integer('deadline_hours'); 
            $table->decimal('penalty_percentage', 5, 2); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};
