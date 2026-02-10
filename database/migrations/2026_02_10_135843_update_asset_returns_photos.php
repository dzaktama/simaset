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
            $table->dropColumn('photo_proof');
            $table->string('photo_proof_1')->nullable()->after('notes');
            $table->string('photo_proof_2')->nullable()->after('photo_proof_1');
            $table->string('photo_proof_3')->nullable()->after('photo_proof_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_returns', function (Blueprint $table) {
            $table->dropColumn(['photo_proof_1', 'photo_proof_2', 'photo_proof_3']);
            $table->string('photo_proof')->nullable()->after('notes');
        });
    }
};
