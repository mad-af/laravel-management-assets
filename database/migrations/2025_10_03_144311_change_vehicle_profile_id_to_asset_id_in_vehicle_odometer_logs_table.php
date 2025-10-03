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
        Schema::table('vehicle_odometer_logs', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['vehicle_profile_id']);
            
            // Rename the column from vehicle_profile_id to asset_id
            $table->renameColumn('vehicle_profile_id', 'asset_id');
            
            // Add new foreign key constraint to assets table
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_odometer_logs', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['asset_id']);
            
            // Rename the column back from asset_id to vehicle_profile_id
            $table->renameColumn('asset_id', 'vehicle_profile_id');
            
            // Add back the original foreign key constraint
            $table->foreign('vehicle_profile_id')->references('id')->on('vehicle_profiles')->onDelete('cascade');
        });
    }
};
