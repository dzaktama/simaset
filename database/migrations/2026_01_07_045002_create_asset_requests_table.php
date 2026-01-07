<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_requests', function (Blueprint $table) {
            $table->id();
            // Siapa yang minta?
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Barang apa yang diminta?
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            // Kapan diminta?
            $table->date('request_date')->default(now());
            // Tanggal kembali (opsional, kalau peminjaman sementara)
            $table->date('return_date')->nullable();
            // Status Approval: pending (nunggu), approved (boleh), rejected (tolak), returned (sudah balik)
            $table->enum('status', ['pending', 'approved', 'rejected', 'returned'])->default('pending');
            // Alasan peminjaman (biar Admin yakin)
            $table->text('reason')->nullable();
            // Catatan Admin (misal: "Jangan rusak ya")
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_requests');
    }
};