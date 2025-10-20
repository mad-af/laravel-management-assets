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
            $table->string('owner', 64)->nullable()->after('vin');
            $table->enum('type', ['passenger', 'cargo', 'motorcycle'])->default('passenger')->after('owner');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_profiles', function (Blueprint $table) {
            $table->dropColumn(['owner', 'type']);
        });
    }
};
