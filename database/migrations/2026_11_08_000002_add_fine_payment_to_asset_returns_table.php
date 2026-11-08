<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Alur pembayaran denda pengembalian aset.
 *
 * fine_status: none (tanpa denda) -> unpaid -> proof_submitted -> paid
 *                                          \-> waived (dibebaskan admin)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_returns', function (Blueprint $table) {
            $table->string('fine_status', 20)->default('none')->after('fine');
            $table->string('fine_payment_method', 20)->nullable()->after('fine_status');
            $table->string('fine_payment_proof')->nullable()->after('fine_payment_method');
            $table->dateTime('fine_proof_submitted_at')->nullable()->after('fine_payment_proof');
            $table->dateTime('fine_paid_at')->nullable()->after('fine_proof_submitted_at');
            $table->unsignedBigInteger('fine_confirmed_by')->nullable()->after('fine_paid_at');
            $table->text('fine_note')->nullable()->after('fine_confirmed_by');
            $table->text('admin_note')->nullable()->after('fine_note');
            $table->index('fine_status', 'asset_returns_fine_status_index');
            $table->index('status', 'asset_returns_status_index');
        });

        // Data lama: denda yang sudah tercatat otomatis berstatus "belum dibayar".
        DB::table('asset_returns')
            ->where('status', 'approved')
            ->where('fine', '>', 0)
            ->update(['fine_status' => 'unpaid']);
    }

    public function down(): void
    {
        Schema::table('asset_returns', function (Blueprint $table) {
            $table->dropIndex('asset_returns_fine_status_index');
            $table->dropIndex('asset_returns_status_index');
            $table->dropColumn([
                'fine_status', 'fine_payment_method', 'fine_payment_proof', 'fine_proof_submitted_at',
                'fine_paid_at', 'fine_confirmed_by', 'fine_note', 'admin_note',
            ]);
        });
    }
};
