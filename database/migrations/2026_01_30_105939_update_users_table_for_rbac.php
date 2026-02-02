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
        Schema::table('users', function (Blueprint $table) {
            // 1. Add role_id column (Nullable first to allow data migration)
            $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->onDelete('set null');
        });

        // 2. Migrate Data (Map old string roles to new IDs)
        // We assume the Roles Table is already seeded or we can seed it here if empty.
        // It's safer to use raw SQL for performance and avoiding Model issues in migration.
        
        $roles = \Illuminate\Support\Facades\DB::table('roles')->pluck('id', 'slug');
        
        if ($roles->isNotEmpty()) {
            foreach ($roles as $slug => $id) {
                \Illuminate\Support\Facades\DB::table('users')
                    ->where('role', $slug)
                    ->update(['role_id' => $id]);
            }
        }

        Schema::table('users', function (Blueprint $table) {
            // 3. Drop old columns
            $table->dropColumn(['role', 'permissions']);
            
            // 4. Make role_id required (if desired, but nullable is safer for now or set default)
            // $table->foreignId('role_id')->nullable(false)->change(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
