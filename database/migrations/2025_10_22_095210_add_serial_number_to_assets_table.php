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
        if (Schema::hasTable('assets') && ! Schema::hasColumn('assets', 'serial_number')) {
            Schema::table('assets', function (Blueprint $table) {
                // Add nullable serial_number to assets for all related features to use
                $table->string('serial_number', 128)->nullable()->after('description');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('assets') && Schema::hasColumn('assets', 'serial_number')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->dropColumn('serial_number');
            });
        }
    }
};
