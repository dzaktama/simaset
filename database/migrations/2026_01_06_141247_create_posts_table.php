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
        Schema::create('posts', function (Blueprint $table) {
        $table->id();
        // INI PENTING: Relasi ke tabel Users (Penulisnya siapa?)
        $table->foreignId('user_id')->constrained(); 
        $table->string('title'); // Judul Artikel
        $table->text('body');    // Isi Artikel
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
