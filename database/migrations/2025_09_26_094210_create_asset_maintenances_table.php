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
        Schema::create('asset_maintenances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('asset_id');
            $table->string('title');
            $table->enum('type', ['preventive', 'corrective']);
            $table->enum('status', ['open', 'in_progress', 'completed', 'cancelled'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('estimated_completed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->decimal('cost', 15, 2)->default(0.00);
            $table->string('technician_name')->nullable();
            $table->string('vendor_name')->nullable();
            $table->text('notes')->nullable();
            $table->integer('odometer_km_at_service')->nullable();
            $table->integer('next_service_target_odometer_km')->nullable();
            $table->date('next_service_date')->nullable();
            $table->string('invoice_no')->nullable();
            $table->timestamps();

            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_maintenances');
    }
};
