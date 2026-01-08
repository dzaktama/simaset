<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_id')->nullable()->unique()->after('id'); // NIP / NIK
            $table->string('phone')->nullable()->after('email');
            $table->string('department')->nullable()->after('phone'); // Divisi
            $table->string('position')->nullable()->after('department'); // Jabatan
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['employee_id', 'phone', 'department', 'position']);
        });
    }
};
