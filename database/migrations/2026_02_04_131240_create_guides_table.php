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
        Schema::create('guides', function (Blueprint $table) {
            $table->string('id')->primary(); // Manually assigned ID (e.g., 'basic', 'user-borrow')
            $table->string('title');
            $table->text('description');
            $table->string('icon')->default('book-open');
            $table->string('color')->default('blue');
            $table->json('roles'); // Stores array of allowed roles
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guides');
    }
};
