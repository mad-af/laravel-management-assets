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
            $table->dropColumn('annual_tax_due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_profiles', function (Blueprint $table) {
            $table->date('annual_tax_due_date')->nullable()->after('next_service_date');
        });
    }
};
