<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asset_requests', function (Blueprint $table) {
            // Add borrowing-specific columns
            if (!Schema::hasColumn('asset_requests', 'borrowed_at')) {
                $table->dateTime('borrowed_at')->nullable()->after('return_date');
            }
            if (!Schema::hasColumn('asset_requests', 'returned_at')) {
                $table->dateTime('returned_at')->nullable()->after('borrowed_at');
            }
            if (!Schema::hasColumn('asset_requests', 'approved_at')) {
                $table->dateTime('approved_at')->nullable()->after('returned_at');
            }
            if (!Schema::hasColumn('asset_requests', 'condition')) {
                $table->string('condition')->nullable()->after('approved_at'); // good, minor_damage, major_damage
            }
            if (!Schema::hasColumn('asset_requests', 'return_notes')) {
                $table->text('return_notes')->nullable()->after('condition');
            }
            if (!Schema::hasColumn('asset_requests', 'borrowing_status')) {
                $table->string('borrowing_status')->default('pending')->after('status'); // pending, active, returned, rejected
            }
        });
    }

    public function down(): void
    {
        Schema::table('asset_requests', function (Blueprint $table) {
            $table->dropColumnIfExists(['borrowed_at', 'returned_at', 'approved_at', 'condition', 'return_notes', 'borrowing_status']);
        });
    }
};
