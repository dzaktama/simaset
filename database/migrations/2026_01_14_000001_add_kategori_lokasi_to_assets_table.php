<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // Cek dulu, kalau kolom belum ada, baru dibuat.
            if (!Schema::hasColumn('assets', 'category')) {
                $table->string('category')->nullable()->after('name');
            }
            if (!Schema::hasColumn('assets', 'rak')) {
                $table->string('rak')->nullable()->after('category');
            }
            if (!Schema::hasColumn('assets', 'lorong')) {
                $table->string('lorong')->nullable()->after('rak');
            }
            // Kolom location biasanya sudah ada, tapi kita cek aja biar aman
            if (!Schema::hasColumn('assets', 'location')) {
                $table->string('location')->nullable()->after('lorong');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // INI PERBAIKANNYA: Cek dulu sebelum hapus!
            // Kalau kolomnya ada, baru dihapus. Kalau gak ada, ya udah biarin (jangan error).
            
            if (Schema::hasColumn('assets', 'category')) {
                $table->dropColumn('category');
            }
            if (Schema::hasColumn('assets', 'rak')) {
                $table->dropColumn('rak');
            }
            if (Schema::hasColumn('assets', 'lorong')) {
                $table->dropColumn('lorong');
            }
        });
    }
};