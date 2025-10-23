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
        Schema::create('insurance_claims', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('policy_id');
            $table->uuid('asset_id');
            $table->string('claim_no')->unique();
            $table->date('incident_date');
            $table->enum('incident_type', ['collision', 'theft', 'flood', 'fire', 'other'])->default('other');
            $table->string('incident_other')->nullable();
            $table->text('description')->nullable();
            $table->enum('source', ['manual', 'maintenance'])->default('manual');
            $table->uuid('asset_maintenance_id')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');
            $table->json('claim_documents')->nullable();
            $table->decimal('amount_approved', 15, 2)->nullable();
            $table->decimal('amount_paid', 15, 2);
            $table->uuid('created_by');
            $table->timestamps();

            $table->foreign('policy_id')->references('id')->on('insurance_policies')->onDelete('cascade');
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->foreign('asset_maintenance_id')->references('id')->on('asset_maintenances')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_claims');
    }
};
