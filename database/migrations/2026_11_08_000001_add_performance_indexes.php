<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * [PERF] Index untuk kolom yang sering dipakai di WHERE / GROUP BY / ORDER BY
 * (dashboard, grafik, katalog, laporan). Tanpa index, setiap query COUNT/GROUP BY
 * melakukan full table scan.
 */
return new class extends Migration
{
    /** tabel => [nama_index => kolom[]] */
    private array $indexes = [
        'assets' => [
            'assets_status_index' => ['status'],
            'assets_category_index' => ['category'],
            'assets_created_at_index' => ['created_at'],
            'assets_purchase_date_index' => ['purchase_date'],
        ],
        'asset_requests' => [
            'asset_requests_status_created_at_index' => ['status', 'created_at'],
            'asset_requests_asset_id_status_index' => ['asset_id', 'status'],
            'asset_requests_user_id_status_index' => ['user_id', 'status'],
            'asset_requests_request_date_index' => ['request_date'],
        ],
        'asset_histories' => [
            'asset_histories_created_at_index' => ['created_at'],
            'asset_histories_action_index' => ['action'],
        ],
        'maintenances' => [
            'maintenances_asset_id_status_index' => ['asset_id', 'status'],
            'maintenances_status_completion_date_index' => ['status', 'completion_date'],
        ],
    ];

    public function up(): void
    {
        foreach ($this->indexes as $table => $indexes) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            foreach ($indexes as $name => $columns) {
                if (!$this->columnsExist($table, $columns) || Schema::hasIndex($table, $name)) {
                    continue;
                }

                Schema::table($table, function (Blueprint $blueprint) use ($columns, $name) {
                    $blueprint->index($columns, $name);
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->indexes as $table => $indexes) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            foreach (array_keys($indexes) as $name) {
                if (Schema::hasIndex($table, $name)) {
                    Schema::table($table, function (Blueprint $blueprint) use ($name) {
                        $blueprint->dropIndex($name);
                    });
                }
            }
        }
    }

    private function columnsExist(string $table, array $columns): bool
    {
        foreach ($columns as $column) {
            if (!Schema::hasColumn($table, $column)) {
                return false;
            }
        }

        return true;
    }
};
