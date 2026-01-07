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
        Schema::create('asset_histories', function (Blueprint $table) {
            $table->id();
            // Relasi ke aset yang diubah
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            // Siapa yang melakukan aksi (admin/sistem/user)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            // Jenis aksi: created, status_change, deployed, returned, maintenance
            $table->string('action'); 
            // Catatan detail, misal: "Barang diserahkan ke Budi" atau "Layar retak"
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_histories');
    }
};