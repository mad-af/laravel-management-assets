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
            $table->foreignUuid('company_id')->after('id')->constrained('companies')->onDelete('cascade');
            
            // Drop existing unique constraint on code and add composite unique constraint
            $table->dropUnique(['code']);
            $table->unique(['company_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'code']);
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
            
            // Restore original unique constraint on code
            $table->unique('code');
        });
    }
};
