<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE jurnalings ALTER COLUMN debit TYPE NUMERIC(15,2) USING debit::numeric');
            DB::statement('ALTER TABLE jurnalings ALTER COLUMN debit SET NOT NULL');
            DB::statement('ALTER TABLE jurnalings ALTER COLUMN debit SET DEFAULT 0');
            DB::statement('ALTER TABLE jurnalings ALTER COLUMN kredit TYPE NUMERIC(15,2) USING kredit::numeric');
            DB::statement('ALTER TABLE jurnalings ALTER COLUMN kredit SET NOT NULL');
            DB::statement('ALTER TABLE jurnalings ALTER COLUMN kredit SET DEFAULT 0');
        } else {
            DB::statement('ALTER TABLE jurnalings MODIFY COLUMN debit NUMERIC(15,2) NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE jurnalings MODIFY COLUMN kredit NUMERIC(15,2) NOT NULL DEFAULT 0');
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE jurnalings ALTER COLUMN debit TYPE VARCHAR(255) USING debit::varchar');
            DB::statement('ALTER TABLE jurnalings ALTER COLUMN debit DROP NOT NULL');
            DB::statement('ALTER TABLE jurnalings ALTER COLUMN kredit TYPE VARCHAR(255) USING kredit::varchar');
            DB::statement('ALTER TABLE jurnalings ALTER COLUMN kredit DROP NOT NULL');
        } else {
            DB::statement('ALTER TABLE jurnalings MODIFY COLUMN debit VARCHAR(255) NOT NULL');
            DB::statement('ALTER TABLE jurnalings MODIFY COLUMN kredit VARCHAR(255) NOT NULL');
        }
    }
};
