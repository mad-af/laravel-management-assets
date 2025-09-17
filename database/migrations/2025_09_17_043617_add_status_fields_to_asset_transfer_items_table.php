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
        Schema::table('asset_transfer_items', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('to_location_id');
            $table->text('notes')->nullable()->after('status');
            $table->timestamp('transferred_at')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_transfer_items', function (Blueprint $table) {
            $table->dropColumn(['status', 'notes', 'transferred_at']);
        });
    }
};
