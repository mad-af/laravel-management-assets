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
        Schema::table('asset_transfers', function (Blueprint $table) {
            $table->uuid('from_location_id')->nullable()->after('company_id');
            $table->uuid('to_location_id')->nullable()->after('from_location_id');
            
            $table->foreign('from_location_id')->references('id')->on('locations')->onDelete('set null');
            $table->foreign('to_location_id')->references('id')->on('locations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_transfers', function (Blueprint $table) {
            $table->dropForeign(['from_location_id']);
            $table->dropForeign(['to_location_id']);
            $table->dropColumn(['from_location_id', 'to_location_id']);
        });
    }
};
