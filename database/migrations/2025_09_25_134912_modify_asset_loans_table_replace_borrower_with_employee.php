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
        Schema::table('asset_loans', function (Blueprint $table) {
            // Drop the index first before dropping the column
            $table->dropIndex(['borrower_name', 'checkout_at']);

            // Drop the existing borrower_name column
            $table->dropColumn('borrower_name');

            // Add employee_id foreign key
            $table->foreignUuid('employee_id')->after('asset_id')->constrained('employees')->onDelete('cascade');

            // Update condition fields to use enum
            $table->dropColumn(['condition_out', 'condition_in']);
            $table->enum('condition_out', ['good', 'fair', 'poor'])->after('due_at');
            $table->enum('condition_in', ['good', 'fair', 'poor'])->nullable()->after('condition_out');

            // Add new index
            $table->index(['employee_id', 'checkout_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_loans', function (Blueprint $table) {
            // Drop employee_id foreign key and column
            $table->dropForeign(['employee_id']);
            $table->dropColumn('employee_id');

            // Restore borrower_name
            $table->string('borrower_name')->after('asset_id');

            // Restore condition fields as string
            $table->dropColumn(['condition_out', 'condition_in']);
            $table->string('condition_out')->after('due_at');
            $table->string('condition_in')->nullable()->after('condition_out');

            // Restore indexes
            $table->dropIndex(['employee_id', 'checkout_at']);
            $table->index(['borrower_name', 'checkout_at']);
        });
    }
};
