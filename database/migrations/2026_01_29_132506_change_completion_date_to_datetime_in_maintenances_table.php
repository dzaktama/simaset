<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Mengubah tipe kolom completion_date dari DATE ke DATETIME agar waktu bisa tersimpan.
     */
    public function up(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->dateTime('completion_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintenances', function (Blueprint $table) {
            $table->date('completion_date')->nullable()->change();
        });
    }
};
