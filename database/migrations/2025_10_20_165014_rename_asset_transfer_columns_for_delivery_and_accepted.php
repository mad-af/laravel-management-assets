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
            if (Schema::hasColumn('asset_transfers', 'requested_by')) {
                $table->renameColumn('requested_by', 'delivery_by');
            }
            if (Schema::hasColumn('asset_transfers', 'approved_by')) {
                $table->renameColumn('approved_by', 'accepted_by');
            }
            if (Schema::hasColumn('asset_transfers', 'scheduled_at')) {
                $table->renameColumn('scheduled_at', 'accepted_at');
            }
            if (Schema::hasColumn('asset_transfers', 'executed_at')) {
                $table->renameColumn('executed_at', 'delivery_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_transfers', function (Blueprint $table) {
            if (Schema::hasColumn('asset_transfers', 'delivery_by')) {
                $table->renameColumn('delivery_by', 'requested_by');
            }
            if (Schema::hasColumn('asset_transfers', 'accepted_by')) {
                $table->renameColumn('accepted_by', 'approved_by');
            }
            if (Schema::hasColumn('asset_transfers', 'accepted_at')) {
                $table->renameColumn('accepted_at', 'scheduled_at');
            }
            if (Schema::hasColumn('asset_transfers', 'delivery_at')) {
                $table->renameColumn('delivery_at', 'executed_at');
            }
        });
    }
};
