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
        Schema::table('categories', function (Blueprint $table) {
            $table->uuid('company_id')->after('id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            
            // Drop existing unique constraint on name and add composite unique constraint
            $table->dropUnique(['name']);
            $table->unique(['company_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'name']);
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
            
            // Restore original unique constraint on name
            $table->unique('name');
        });
    }
};
