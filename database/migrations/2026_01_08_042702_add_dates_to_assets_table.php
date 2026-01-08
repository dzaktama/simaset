<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // Menyimpan tanggal saat ini barang dipinjamkan
            $table->dateTime('assigned_date')->nullable()->after('user_id');
            // Menyimpan deadline pengembalian (bisa diedit admin)
            $table->dateTime('return_date')->nullable()->after('assigned_date');
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['assigned_date', 'return_date']);
        });
    }
};
