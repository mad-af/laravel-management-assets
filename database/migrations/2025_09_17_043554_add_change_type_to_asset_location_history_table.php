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
        Schema::table('asset_location_history', function (Blueprint $table) {
            $table->string('change_type')->default('transfer')->after('transfer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_location_history', function (Blueprint $table) {
            $table->dropColumn('change_type');
        });
    }
};
