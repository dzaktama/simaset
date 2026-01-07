<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // Menambah kolom untuk 3 foto kondisi
            $table->string('image2')->nullable()->after('image');
            $table->string('image3')->nullable()->after('image2');
            
            // Menambah kolom kondisi fisik detail
            $table->text('condition_notes')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['image2', 'image3', 'condition_notes']);
        });
    }
};