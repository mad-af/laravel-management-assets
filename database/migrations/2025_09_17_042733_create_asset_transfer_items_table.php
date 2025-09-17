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
            $table->foreignUuid('asset_transfer_id')->constrained('asset_transfers')->onDelete('cascade');
            $table->foreignUuid('asset_id')->constrained('assets')->onDelete('cascade');
            $table->foreignUuid('from_location_id')->constrained('locations')->onDelete('cascade');
            $table->foreignUuid('to_location_id')->constrained('locations')->onDelete('cascade');
            $table->timestamps();
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
