<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom user_id untuk melacak siapa yang membuat tiket perbaikan (teknisi)
     */
    public function up(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('asset_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
