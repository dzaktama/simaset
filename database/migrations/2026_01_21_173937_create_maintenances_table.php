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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->onDelete('cascade');
            $table->string('vendor_name');
            $table->date('start_date');
            $table->date('completion_date')->nullable();
            $table->decimal('cost', 15, 2)->default(0);
            $table->text('problem_description');
            $table->text('resolution_notes')->nullable();
            $table->enum('status', ['pending', 'on_process', 'completed', 'cancelled'])->default('on_process');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
