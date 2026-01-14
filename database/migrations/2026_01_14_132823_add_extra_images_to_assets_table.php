<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('assets', function (Blueprint $table) {
        // Tambahkan kolom jika belum ada
        if (!Schema::hasColumn('assets', 'image2')) {
            $table->string('image2')->nullable()->after('image');
        }
        if (!Schema::hasColumn('assets', 'image3')) {
            $table->string('image3')->nullable()->after('image2');
        }
        if (!Schema::hasColumn('assets', 'condition_notes')) {
            $table->text('condition_notes')->nullable()->after('status');
        }
        if (!Schema::hasColumn('assets', 'description')) { // Jaga-jaga kalau belum ada
            $table->text('description')->nullable()->after('name');
        }
    });
}

public function down()
{
    Schema::table('assets', function (Blueprint $table) {
        $table->dropColumn(['image2', 'image3', 'condition_notes', 'description']);
    });
}
};
