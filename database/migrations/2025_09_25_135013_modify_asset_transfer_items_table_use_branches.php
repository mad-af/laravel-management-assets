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
        Schema::table('asset_transfer_items', function (Blueprint $table) {
            // Drop existing location foreign keys and columns
            $table->dropForeign(['from_location_id']);
            $table->dropForeign(['to_location_id']);
            $table->dropColumn(['from_location_id', 'to_location_id']);
            
            // Add branch foreign keys
            $table->foreignUuid('from_branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignUuid('to_branch_id')->constrained('branches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_transfer_items', function (Blueprint $table) {
            // Drop branch foreign keys and columns
            $table->dropForeign(['from_branch_id']);
            $table->dropForeign(['to_branch_id']);
            $table->dropColumn(['from_branch_id', 'to_branch_id']);
            
            // Restore location foreign keys
            $table->foreignUuid('from_location_id')->constrained('locations')->onDelete('cascade');
            $table->foreignUuid('to_location_id')->constrained('locations')->onDelete('cascade');
        });
    }
};
