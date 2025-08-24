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
            // Drop index that uses borrower_id first
            $table->dropIndex(['borrower_id', 'checkout_at']);
            // Drop foreign key constraint
            $table->dropForeign(['borrower_id']);
            // Drop the borrower_id column
            $table->dropColumn('borrower_id');
            // Add borrower_name column
            $table->string('borrower_name')->after('asset_id');
            // Add new index with borrower_name
            $table->index(['borrower_name', 'checkout_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_loans', function (Blueprint $table) {
            // Drop index with borrower_name
            $table->dropIndex(['borrower_name', 'checkout_at']);
            // Drop borrower_name column
            $table->dropColumn('borrower_name');
            // Add back borrower_id column
            $table->uuid('borrower_id')->after('asset_id');
            // Add back foreign key constraint
            $table->foreign('borrower_id')->references('id')->on('users')->onDelete('cascade');
            // Add back index with borrower_id
            $table->index(['borrower_id', 'checkout_at']);
        });
    }
};
