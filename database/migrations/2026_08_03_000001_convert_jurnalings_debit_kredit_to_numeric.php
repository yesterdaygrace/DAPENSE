<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE jurnalings MODIFY COLUMN debit NUMERIC(15,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE jurnalings MODIFY COLUMN kredit NUMERIC(15,2) NOT NULL DEFAULT 0');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE jurnalings MODIFY COLUMN debit VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE jurnalings MODIFY COLUMN kredit VARCHAR(255) NOT NULL');
    }
};
