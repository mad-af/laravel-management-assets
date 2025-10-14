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
            $table->uuid('asset_id')->primary();
            $table->integer('year_purchase')->nullable();
            $table->integer('year_manufacture')->nullable();
            $table->integer('current_odometer_km')->default(0);
            $table->date('last_service_date')->nullable();
            $table->integer('service_target_odometer_km')->nullable();
            $table->date('next_service_date')->nullable();
            $table->date('annual_tax_due_date')->nullable();
            $table->string('plate_no', 32)->nullable();
            $table->string('vin', 64)->nullable();
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
