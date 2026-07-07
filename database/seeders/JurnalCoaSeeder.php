<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JurnalCoaSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::table('jurnalings')->truncate();
        DB::table('saldo_awal')->truncate();
        DB::table('coas')->truncate();
        DB::table('periodes')->truncate();
        DB::table('header_coas')->truncate();

        $sql = file_get_contents(database_path('seed_data_jurnal_coa.sql'));
        DB::unprepared($sql);

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
