<?php

use App\Models\Jurnaling;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

test('aggregate query on jurnalings returns numeric totals without error', function () {
    // This is the exact query that was crashing with SQLSTATE 42883
    // "function sum(character varying) does not exist"
    // After migration, jurnalings.debit/kredit are NUMERIC(15,2) and this must pass.

    $result = Jurnaling::select(
        DB::raw('COUNT(*) as total_entries'),
        DB::raw('COALESCE(SUM(debit), 0) as total_debit'),
        DB::raw('COALESCE(SUM(kredit), 0) as total_kredit')
    )->first();

    expect($result)->not->toBeNull();
    expect($result->total_entries)->toBeNumeric();
    expect($result->total_debit)->toBeNumeric();
    expect($result->total_kredit)->toBeNumeric();
    expect($result->total_entries)->toBeGreaterThan(0);
});

test('debit minus kredit aggregate returns numeric', function () {
    // Tests SUM(debit - kredit) — the other failing pattern in BukuBesarController
    $result = Jurnaling::select(
        DB::raw('SUM(debit - kredit) as saldo_awal')
    )->value('saldo_awal');

    expect($result)->toBeNumeric();
});
