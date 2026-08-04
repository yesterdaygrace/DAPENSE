<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JurnalCoaSeeder extends Seeder
{
    /** @var list<string> */
    private const TABLES = ['jurnalings', 'saldo_awal', 'coas', 'periodes', 'header_coas'];

    public function run(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            foreach (self::TABLES as $table) {
                DB::table($table)->truncate();
            }
        } else {
            // PostgreSQL cannot truncate per-table when FK references exist;
            // one statement with CASCADE satisfies all constraints at once.
            DB::statement('TRUNCATE TABLE ' . implode(', ', self::TABLES) . ' RESTART IDENTITY CASCADE');
        }

        $sql = file_get_contents(database_path('seed_data_jurnal_coa.sql'));
        DB::unprepared($sql);

        if ($driver !== 'mysql') {
            // The seed inserts explicit ids, which does not advance PG sequences.
            foreach (self::TABLES as $table) {
                DB::statement("SELECT setval(pg_get_serial_sequence('{$table}', 'id'), COALESCE((SELECT MAX(id) FROM {$table}), 1))");
            }

            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
