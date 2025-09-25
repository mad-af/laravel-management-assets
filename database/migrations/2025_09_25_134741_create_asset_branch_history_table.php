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
        Schema::create('asset_branch_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('asset_id')->constrained('assets')->onDelete('cascade');
            $table->foreignUuid('from_branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->foreignUuid('to_branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignUuid('transfer_id')->nullable()->constrained('asset_transfers')->onDelete('set null');
            $table->string('remark')->nullable();
            $table->timestamps();
            
            $table->index(['asset_id', 'created_at']);
            $table->index('transfer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_branch_history');
    }
};
