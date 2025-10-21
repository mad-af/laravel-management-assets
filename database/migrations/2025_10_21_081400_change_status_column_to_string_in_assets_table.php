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
        // Ubah tipe kolom `status` menjadi string pada tabel assets
        if (Schema::hasColumn('assets', 'status')) {
            Schema::table('assets', function (Blueprint $table) {
                // Panjang 32 cukup untuk nilai status saat ini
                $table->string('status', 32)->default('active')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Balikkan ke enum seperti definisi awal jika diperlukan
        if (Schema::hasColumn('assets', 'status')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->enum('status', ['active', 'inactive', 'lost', 'maintenance', 'on_loan'])->default('active')->change();
            });
        }
    }
};
