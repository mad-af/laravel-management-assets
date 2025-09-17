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
        Schema::table('asset_transfers', function (Blueprint $table) {
            $table->timestamp('requested_at')->nullable()->after('requested_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_transfers', function (Blueprint $table) {
            $table->dropColumn('requested_at');
        });
    }
};
