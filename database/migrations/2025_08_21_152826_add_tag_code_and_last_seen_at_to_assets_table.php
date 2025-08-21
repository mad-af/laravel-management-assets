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
            $table->string('tag_code')->unique()->nullable()->after('code');
            $table->timestamp('last_seen_at')->nullable()->after('description');
            
            $table->index('tag_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropIndex(['tag_code']);
            $table->dropColumn(['tag_code', 'last_seen_at']);
        });
    }
};
