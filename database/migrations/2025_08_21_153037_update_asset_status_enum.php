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
        Schema::table('assets', function (Blueprint $table) {
            $table->dropIndex(['status', 'condition']);
            $table->dropColumn('status');
        });
        
        Schema::table('assets', function (Blueprint $table) {
            $table->enum('status', ['active', 'damaged', 'lost', 'maintenance', 'checked_out'])
                  ->default('active')
                  ->after('location_id');
            $table->index(['status', 'condition']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropIndex(['status', 'condition']);
            $table->dropColumn('status');
        });
        
        Schema::table('assets', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'maintenance', 'disposed'])
                  ->default('active')
                  ->after('location_id');
            $table->index(['status', 'condition']);
        });
    }
};
