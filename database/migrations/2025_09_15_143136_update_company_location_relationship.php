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
        // Remove company_id from locations table
        Schema::table('locations', function (Blueprint $table) {
            // Drop composite unique constraint first
            $table->dropUnique(['company_id', 'name']);
            // Drop foreign key constraint
            $table->dropForeign(['company_id']);
            // Drop the column
            $table->dropColumn('company_id');
            // Restore original unique constraint on name
            $table->unique('name');
        });

        // Add location_id to companies table
        Schema::table('companies', function (Blueprint $table) {
            $table->uuid('location_id')->nullable()->after('code');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove location_id from companies table
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn('location_id');
        });

        // Add company_id back to locations table
        Schema::table('locations', function (Blueprint $table) {
            // Drop unique constraint on name first
            $table->dropUnique(['name']);
            // Add company_id column
            $table->uuid('company_id')->nullable()->after('id');
            // Add foreign key constraint
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            // Add composite unique constraint
            $table->unique(['company_id', 'name']);
        });
    }
};
