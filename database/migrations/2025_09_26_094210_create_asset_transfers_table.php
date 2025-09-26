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
        Schema::create('asset_transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id');
            $table->string('transfer_no')->unique();
            $table->string('reason')->nullable();
            $table->enum('status', ['shipped', 'delivered'])->default('shipped');
            $table->enum('type', ['branch', 'company'])->default('branch');
            $table->uuid('from_branch_id')->nullable();
            $table->uuid('to_branch_id')->nullable();
            $table->uuid('requested_by');
            $table->uuid('approved_by')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('executed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('from_branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('to_branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_transfers');
    }
};
