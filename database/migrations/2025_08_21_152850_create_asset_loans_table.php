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
        Schema::create('asset_loans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('asset_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('borrower_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamp('checkout_at');
            $table->timestamp('due_at');
            $table->timestamp('checkin_at')->nullable();
            $table->string('condition_out');
            $table->string('condition_in')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['asset_id', 'checkout_at']);
            $table->index(['borrower_id', 'checkout_at']);
            $table->index('due_at');
            $table->index('checkin_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_loans');
    }
};
