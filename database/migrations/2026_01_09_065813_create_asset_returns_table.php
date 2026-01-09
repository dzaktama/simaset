<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_returns', function (Blueprint $table) {
            $table->id();
            // Link ke Request Peminjaman Asli (Biar tau history-nya)
            $table->foreignId('asset_request_id')->constrained()->cascadeOnDelete();
            
            // Link ke User & Aset (Redudansi tapi mempercepat query)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            
            $table->dateTime('return_date'); // Tanggal user klik kembalikan
            $table->string('condition'); // 'good', 'broken', 'maintenance'
            $table->text('notes')->nullable(); // Catatan user (misal: "Lecet dikit bang")
            $table->string('status')->default('pending'); // pending (tunggu admin), approved (selesai), rejected
            
            // Admin yang memverifikasi (Nullable, diisi saat admin klik terima)
            $table->unsignedBigInteger('admin_id')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_returns');
    }
};