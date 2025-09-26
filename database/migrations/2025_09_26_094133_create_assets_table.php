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
            $table->uuid('company_id');
            $table->string('code')->unique();
            $table->string('tag_code')->unique()->nullable();
            $table->string('name');
            $table->uuid('category_id');
            $table->uuid('branch_id');
            $table->string('brand', 64)->nullable();
            $table->string('model', 64)->nullable();
            $table->enum('status', ['active', 'inactive', 'lost', 'maintenance', 'on_loan'])->default('active');
            $table->enum('condition', ['good', 'fair', 'poor'])->default('good');
            $table->decimal('value', 15, 2);
            $table->date('purchase_date')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('restrict');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('restrict');
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
