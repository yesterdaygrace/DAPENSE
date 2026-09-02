<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Drop the UNIQUE (nomor_bukti, periode_id) constraint on jurnalings.
 *
 * The constraint conflicts with double-entry bookkeeping: one voucher
 * (nomor_bukti) legitimately spans multiple rows (debit + credit lines),
 * which the seed data and the legacy JurnalingController::storeEntry rely on.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jurnalings', function (Blueprint $table) {
            $indexes = Schema::getIndexes('jurnalings');
            $candidates = ['jurnalings_nomor_bukti_periode_unique', 'jurnaling_nomor_bukti_periode_unique'];
            foreach ($indexes as $index) {
                if (in_array($index['name'], $candidates, true)) {
                    $table->dropUnique($index['name']);
                    break;
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('jurnalings', function (Blueprint $table) {
            $table->unique(['nomor_bukti', 'periode_id'], 'jurnalings_nomor_bukti_periode_unique');
        });
    }
};
