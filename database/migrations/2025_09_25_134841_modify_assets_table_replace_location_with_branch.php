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
        // Check if columns already exist before adding them
        if (! Schema::hasColumn('assets', 'branch_id')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->uuid('branch_id')->nullable();
            });
        }

        if (! Schema::hasColumn('assets', 'brand')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->string('brand')->nullable();
            });
        }

        if (! Schema::hasColumn('assets', 'model')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->string('model')->nullable();
            });
        }

        // Add foreign key constraint if branch_id column exists and doesn't have foreign key
        if (Schema::hasColumn('assets', 'branch_id')) {
            try {
                Schema::table('assets', function (Blueprint $table) {
                    $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist, continue
            }
        }

        // Drop existing index that references status and condition columns
        try {
            Schema::table('assets', function (Blueprint $table) {
                $table->dropIndex(['status', 'condition']);
            });
        } catch (\Exception $e) {
            // Index might not exist, continue
        }

        // Update enums in a separate schema operation
        Schema::table('assets', function (Blueprint $table) {
            // Update status enum
            $table->dropColumn('status');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'lost', 'maintenance', 'on_loan'])->default('active');
        });

        Schema::table('assets', function (Blueprint $table) {
            // Update condition enum
            $table->dropColumn('condition');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->enum('condition', ['good', 'fair', 'poor'])->default('good');
        });

        // Add indexes
        Schema::table('assets', function (Blueprint $table) {
            $table->index(['status', 'condition']);
            $table->index('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['branch_id']);
            $table->dropIndex(['status', 'condition']);

            // Drop foreign key
            $table->dropForeign(['branch_id']);

            // Drop new columns
            $table->dropColumn(['branch_id', 'brand', 'model']);
        });

        // Restore original enums
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'lost', 'maintenance'])->default('active');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn('condition');
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->enum('condition', ['good', 'fair', 'poor'])->default('good');
        });
    }
};
