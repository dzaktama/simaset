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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            // Relasi ke User (Holder/Peminjam). 
            // Nullable artinya barang bisa saja sedang di Gudang (tidak dipegang siapa-siapa).
            // onDelete('set null') artinya jika karyawan resign (dihapus), data aset tidak ikut terhapus, tapi jadi 'no holder'.
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Identitas Barang
            $table->string('name'); // Contoh: MacBook Pro M3
            $table->string('serial_number')->unique(); // SN Wajib Unik
            
            // Status Barang (Enum untuk konsistensi data)
            $table->enum('status', ['available', 'deployed', 'maintenance', 'broken'])->default('available');
            
            // Detail Fisik & Administrasi
            $table->text('description')->nullable(); // Spek detail
            $table->date('purchase_date')->nullable(); // Tgl Pembelian untuk hitung depresiasi
            $table->string('image')->nullable(); // Foto barang
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};