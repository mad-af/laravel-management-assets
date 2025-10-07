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
        Schema::create('vehicle_tax_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('vehicle_tax_type_id');
            $table->uuid('asset_id');
            $table->integer('year');
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('receipt_no', 64)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('vehicle_tax_type_id')->references('id')->on('vehicle_tax_types')->onDelete('cascade');
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_tax_histories');
    }
};
