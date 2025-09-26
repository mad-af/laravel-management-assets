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
            $table->uuid('asset_id');
            $table->uuid('from_branch_id')->nullable();
            $table->uuid('to_branch_id');
            $table->uuid('transfer_id')->nullable();
            $table->string('remark')->nullable();

            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->foreign('from_branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('to_branch_id')->references('id')->on('branches')->onDelete('restrict');
            $table->foreign('transfer_id')->references('id')->on('asset_transfers')->onDelete('set null');
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
