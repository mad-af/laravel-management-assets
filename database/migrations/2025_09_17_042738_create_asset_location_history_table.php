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
        Schema::create('asset_location_history', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('asset_id')->constrained('assets')->onDelete('cascade');
            $table->foreignUuid('from_location_id')->nullable()->constrained('locations')->onDelete('cascade');
            $table->foreignUuid('to_location_id')->constrained('locations')->onDelete('cascade');
            $table->timestamp('changed_at');
            $table->foreignUuid('changed_by')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('transfer_id')->nullable()->constrained('asset_transfers')->onDelete('set null');
            $table->string('remark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_location_history');
    }
};
