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
        Schema::create('vehicle_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('asset_id');
            $table->string('license_plate')->nullable();
            $table->string('engine_number')->nullable();
            $table->string('chassis_number')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('transmission')->nullable();
            $table->integer('year')->nullable();
            $table->string('color')->nullable();
            $table->integer('odometer_km')->nullable();
            $table->timestamps();

            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_profiles');
    }
};
