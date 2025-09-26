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
        Schema::create('asset_transfer_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('asset_transfer_id');
            $table->uuid('asset_id');
            $table->uuid('from_branch_id');
            $table->uuid('to_branch_id');
            $table->timestamps();

            $table->foreign('asset_transfer_id')->references('id')->on('asset_transfers')->onDelete('cascade');
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->foreign('from_branch_id')->references('id')->on('branches')->onDelete('restrict');
            $table->foreign('to_branch_id')->references('id')->on('branches')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_transfer_items');
    }
};
