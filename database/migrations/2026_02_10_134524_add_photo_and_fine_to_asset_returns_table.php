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
        Schema::table('asset_returns', function (Blueprint $table) {
            $table->string('photo_proof')->nullable()->after('notes'); // Foto bukti kondisi
            $table->decimal('fine', 15, 2)->default(0)->after('photo_proof'); // Denda kerusakan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_returns', function (Blueprint $table) {
            $table->dropColumn(['photo_proof', 'fine']);
        });
    }
};
