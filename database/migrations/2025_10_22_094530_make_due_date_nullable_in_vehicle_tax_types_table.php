<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('vehicle_tax_types', 'due_date')) {
            // Primary attempt using Laravel's schema builder (requires doctrine/dbal)
            Schema::table('vehicle_tax_types', function (Blueprint $table) {
                $table->date('due_date')->nullable()->change();
            });

            // Enforce at the SQL level for production robustness
            $driver = DB::getDriverName();

            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE vehicle_tax_types MODIFY due_date DATE NULL');
            } elseif ($driver === 'pgsql') {
                DB::statement('ALTER TABLE vehicle_tax_types ALTER COLUMN due_date DROP NOT NULL');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('vehicle_tax_types', 'due_date')) {
            Schema::table('vehicle_tax_types', function (Blueprint $table) {
                $table->date('due_date')->nullable(false)->change();
            });

            $driver = DB::getDriverName();

            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE vehicle_tax_types MODIFY due_date DATE NOT NULL');
            } elseif ($driver === 'pgsql') {
                DB::statement('ALTER TABLE vehicle_tax_types ALTER COLUMN due_date SET NOT NULL');
            }
        }
    }
};
