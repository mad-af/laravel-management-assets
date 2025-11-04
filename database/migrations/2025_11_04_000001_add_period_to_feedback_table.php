<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->string('period', 32)->nullable()->after('user_id');
            $table->unique(['user_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'period']);
            $table->dropColumn('period');
        });
    }
};