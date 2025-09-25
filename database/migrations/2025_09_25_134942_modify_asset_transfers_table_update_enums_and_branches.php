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
            // Update status enum to match new schema
            $table->dropColumn('status');
            $table->enum('status', ['shipped', 'delivered'])->default('shipped')->after('reason');
            
            // Update type enum to match new schema
            $table->dropColumn('type');
            $table->enum('type', ['branch', 'company'])->default('branch')->after('status');
            
            // Add branch fields
            $table->foreignUuid('from_branch_id')->nullable()->after('type')->constrained('branches')->onDelete('set null');
            $table->foreignUuid('to_branch_id')->nullable()->after('from_branch_id')->constrained('branches')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_transfers', function (Blueprint $table) {
            // Drop branch fields
            $table->dropForeign(['from_branch_id']);
            $table->dropForeign(['to_branch_id']);
            $table->dropColumn(['from_branch_id', 'to_branch_id']);
            
            // Restore original status enum
            $table->dropColumn('status');
            $table->enum('status', ['draft', 'submitted', 'approved', 'executed', 'void'])->default('draft');
            
            // Restore original type enum
            $table->dropColumn('type');
            $table->enum('type', ['internal', 'external'])->default('internal');
        });
    }
};
