<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Tambah kolom kategori_barang dan lokasi ke tabel assets
     */
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('kategori_barang')->nullable()->after('name'); // kategori barang
            $table->string('rak')->nullable()->after('kategori_barang'); // rak
            $table->string('lorong')->nullable()->after('rak'); // lorong
            $table->string('keterangan_lokasi')->nullable()->after('lorong'); // keterangan lokasi
        });
    }

    /**
     * Rollback perubahan
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['kategori_barang', 'rak', 'lorong', 'keterangan_lokasi']);
        });
    }
};
