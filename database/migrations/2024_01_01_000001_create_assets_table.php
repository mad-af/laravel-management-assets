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
        Schema::create('assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->foreignUuid('category_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('location_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['active', 'inactive', 'maintenance', 'disposed'])->default('active');
            $table->enum('condition', ['excellent', 'good', 'fair', 'poor'])->default('good');
            $table->decimal('value', 15, 2);
            $table->date('purchase_date')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'condition']);
            $table->index('category_id');
            $table->index('location_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};