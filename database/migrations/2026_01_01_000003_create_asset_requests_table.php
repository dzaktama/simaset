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
            // Relasi ke User (Peminjam)
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            // Relasi ke Asset (Barang yg dipinjam) - INI YG BIKIN ERROR KEMAREN
            // Sekarang aman karena file assets (02) dijalankan sebelum file ini (03)
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            
            $table->date('request_date');
            $table->date('return_date')->nullable();
            $table->string('status')->default('pending'); // pending, approved, rejected, returned
            $table->text('reason')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_requests');
    }
};