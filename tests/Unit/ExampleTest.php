<?php

use App\Models\COA;
use App\Models\HeaderCOA;
use App\Models\Jurnaling;
use App\Models\NeracaSaldo;
use App\Models\Otorisator;
use App\Models\Periode;
use App\Models\SaldoAwal;
use App\Models\User;

test('user has correct fillable attributes', function () {
    $user = new User;
    expect($user->getFillable())->toContain('name', 'email', 'password', 'usertype', 'status', 'image');
});

test('user has correct hidden attributes', function () {
    $user = new User;
    expect($user->getHidden())->toContain('password', 'remember_token');
});

test('periode has fillable attributes', function () {
    $periode = new Periode;
    expect($periode->getFillable())->toContain('nama_periode', 'tanggal_awal', 'tanggal_akhir', 'is_rekap');
});

test('COA has fillable attributes', function () {
    $coa = new COA;
    expect($coa->getFillable())->toContain('kode_akun', 'nama_akun', 'saldo_normal', 'kategori', 'level', 'header_coa_id');
});

test('HeaderCOA has fillable attributes', function () {
    $header = new HeaderCOA;
    expect($header->getFillable())->toContain('kode_header', 'nama_header', 'level', 'parent_id');
});

test('Jurnaling has fillable attributes', function () {
    $jurnaling = new Jurnaling;
    expect($jurnaling->getFillable())->toContain(
        'tanggal_jurnal', 'nomor_bukti', 'keterangan', 'kategori_jurnal',
        'debit', 'kredit', 'coa_id', 'periode_id'
    );
});

test('SaldoAwal has fillable attributes', function () {
    $saldoawal = new SaldoAwal;
    expect($saldoawal->getFillable())->toContain('coa_id', 'tanggal_saldo', 'debit', 'kredit', 'periode_id');
});

test('Otorisator has fillable attributes', function () {
    $otorisator = new Otorisator;
    expect($otorisator->getFillable())->toContain('nama_otorisator', 'jabatan_otorisator');
});

test('user model casts email_verified_at as datetime', function () {
    $user = new User;
    expect($user->getCasts())->toHaveKey('email_verified_at', 'datetime');
});

test('user model has isActive method', function () {
    $user = new User;
    expect(method_exists($user, 'isActive'))->toBeTrue();
});

test('isActive returns true when status is 1', function () {
    $user = new User;
    $user->status = 1;
    expect($user->isActive())->toBeTrue();
});

test('isActive returns false when status is 0', function () {
    $user = new User;
    $user->status = 0;
    expect($user->isActive())->toBeFalse();
});

test('user types are valid strings', function () {
    $validTypes = ['rootsuperuser', 'admin', 'operator', 'bod'];
    $user = new User;
    $user->usertype = 'admin';
    expect($user->usertype)->toBeIn($validTypes);
});

test('user status is numeric', function () {
    $user = new User;
    $user->status = 1;
    expect($user->status)->toBeNumeric();
});

test('model classes exist', function () {
    expect(class_exists(User::class))->toBeTrue();
    expect(class_exists(Periode::class))->toBeTrue();
    expect(class_exists(COA::class))->toBeTrue();
    expect(class_exists(HeaderCOA::class))->toBeTrue();
    expect(class_exists(Jurnaling::class))->toBeTrue();
    expect(class_exists(SaldoAwal::class))->toBeTrue();
    expect(class_exists(Otorisator::class))->toBeTrue();
    expect(class_exists(NeracaSaldo::class))->toBeTrue();
});
