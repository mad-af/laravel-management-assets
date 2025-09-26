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
        Schema::create('vehicle_odometer_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vehicle_profile_id');
            $table->integer('odometer_km');
            $table->enum('source', ['manual', 'maintenance']);
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->foreign('vehicle_profile_id')->references('id')->on('vehicle_profiles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_odometer_logs');
    }
};
