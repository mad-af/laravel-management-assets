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
        Schema::table('vehicle_profiles', function (Blueprint $table) {
            // Make all columns nullable except asset_id and current_odometer_km
            $table->integer('year_purchase')->nullable()->change();
            $table->integer('year_manufacture')->nullable()->change();
            $table->date('last_service_date')->nullable()->change();
            $table->integer('service_interval_km')->nullable()->change();
            $table->integer('service_interval_days')->nullable()->change();
            $table->integer('service_target_odometer_km')->nullable()->change();
            $table->date('next_service_date')->nullable()->change();
            $table->date('annual_tax_due_date')->nullable()->change();
            $table->string('plate_no')->nullable()->change();
            $table->string('vin')->nullable()->change();
            $table->string('brand')->nullable()->change();
            $table->string('model')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_profiles', function (Blueprint $table) {
            // Revert columns back to not nullable
            $table->integer('year_purchase')->nullable(false)->change();
            $table->integer('year_manufacture')->nullable(false)->change();
            $table->date('last_service_date')->nullable(false)->change();
            $table->integer('service_interval_km')->nullable(false)->change();
            $table->integer('service_interval_days')->nullable(false)->change();
            $table->integer('service_target_odometer_km')->nullable(false)->change();
            $table->date('next_service_date')->nullable(false)->change();
            $table->date('annual_tax_due_date')->nullable(false)->change();
            $table->string('plate_no')->nullable(false)->change();
            $table->string('vin')->nullable(false)->change();
            $table->string('brand')->nullable(false)->change();
            $table->string('model')->nullable(false)->change();
        });
    }
};
