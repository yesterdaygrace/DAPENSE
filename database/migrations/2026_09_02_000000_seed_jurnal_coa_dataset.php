<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLES = ['jurnalings', 'saldo_awal', 'coas', 'periodes', 'header_coas'];

    /**
     * Seed canonical dataset from database/seed_data_jurnal_coa.sql.
     * Idempotent: skips when header_coas already populated (so db:seed remains authoritative
     * and re-running migrate does not duplicate). The SQL itself is driver-agnostic
     * (standard INSERTs, no backticks, no MySQL-only syntax) and works on both mysql and pgsql.
     */
    public function up(): void
    {
        if (Schema::hasTable('header_coas') && DB::table('header_coas')->exists()) {
            return;
        }

        $path = database_path('seed_data_jurnal_coa.sql');
        if (! file_exists($path)) {
            return;
        }

        $sql = file_get_contents($path);
        DB::unprepared($sql);

        if (DB::connection()->getDriverName() !== 'mysql') {
            foreach (self::TABLES as $table) {
                DB::statement("SELECT setval(pg_get_serial_sequence('{$table}', 'id'), COALESCE((SELECT MAX(id) FROM {$table}), 1))");
            }
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            foreach (self::TABLES as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                }
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        } else {
            $existing = array_filter(self::TABLES, fn ($t) => Schema::hasTable($t));
            if ($existing !== []) {
                DB::statement('TRUNCATE TABLE ' . implode(', ', $existing) . ' RESTART IDENTITY CASCADE');
            }
        }
    }
};
