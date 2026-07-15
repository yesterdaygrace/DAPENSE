<?php

use App\Models\HeaderCOA;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $this->admin = User::factory()->create(['usertype' => 'admin', 'status' => 1]);
    $this->actingAs($this->admin);
});

test('periode requires nama field', function () {
    $response = $this->post('/admin/periodes/save', [
        'tanggal_awal' => '2024-01-01',
        'tanggal_akhir' => '2024-12-31',
    ]);
    $response->assertSessionHasErrors('nama_periode');
});

test('periode requires tanggal_awal field', function () {
    $response = $this->post('/admin/periodes/save', [
        'nama_periode' => 'Test Periode',
        'tanggal_akhir' => '2024-12-31',
    ]);
    $response->assertSessionHasErrors('tanggal_awal');
});

test('periode requires tanggal_akhir field', function () {
    $response = $this->post('/admin/periodes/save', [
        'nama_periode' => 'Test Periode',
        'tanggal_awal' => '2024-01-01',
    ]);
    $response->assertSessionHasErrors('tanggal_akhir');
});

test('tanggal_akhir must be after tanggal_awal', function () {
    $response = $this->post('/admin/periodes/save', [
        'nama_periode' => 'Test Periode',
        'tanggal_awal' => '2024-12-31',
        'tanggal_akhir' => '2024-01-01',
    ]);
    $response->assertSessionHasErrors();
});

test('COA requires kode_akun', function () {
    $header = HeaderCOA::create([
        'kode_header' => 'H001',
        'nama_header' => 'Test Header',
        'level' => 1,
    ]);
    $response = $this->post('/admin/account/coa/save', [
        'nama_akun' => 'Test Akun',
        'saldo_normal' => 'Debit',
        'kategori' => 'Aktiva',
        'level' => '1',
        'header_id' => $header->id,
    ]);
    $response->assertSessionHasErrors('kode_akun');
});

test('COA requires nama_akun', function () {
    $header = HeaderCOA::create([
        'kode_header' => 'H001',
        'nama_header' => 'Test Header',
        'level' => 1,
    ]);
    $response = $this->post('/admin/account/coa/save', [
        'kode_akun' => '10001',
        'saldo_normal' => 'Debit',
        'kategori' => 'Aktiva',
        'level' => '1',
        'header_id' => $header->id,
    ]);
    $response->assertSessionHasErrors('nama_akun');
});

test('COA requires valid saldo_normal', function () {
    $header = HeaderCOA::create([
        'kode_header' => 'H001',
        'nama_header' => 'Test Header',
        'level' => 1,
    ]);
    $response = $this->post('/admin/account/coa/save', [
        'kode_akun' => '10001',
        'nama_akun' => 'Test Akun',
        'saldo_normal' => 'InvalidValue',
        'kategori' => 'Aktiva',
        'level' => '1',
        'header_id' => $header->id,
    ]);
    $response->assertSessionHasErrors('saldo_normal');
});

test('Header COA requires kode_header', function () {
    $response = $this->post('/admin/account/header/save', [
        'nama_header' => 'Test Header',
        'level' => '1',
    ]);
    $response->assertSessionHasErrors('kode_header');
});

test('Header COA requires nama_header', function () {
    $response = $this->post('/admin/account/header/save', [
        'kode_header' => 'H001',
        'level' => '1',
    ]);
    $response->assertSessionHasErrors('nama_header');
});

test('Otorisator requires nama_otorisator', function () {
    $response = $this->post('/admin/otorisator/save', [
        'jabatan_otorisator' => 'Manager',
    ]);
    $response->assertSessionHasErrors('nama_otorisator');
});

test('Otorisator requires jabatan_otorisator', function () {
    $response = $this->post('/admin/otorisator/save', [
        'nama_otorisator' => 'John Doe',
    ]);
    $response->assertSessionHasErrors('jabatan_otorisator');
});

test('user requires name for registration', function () {
    Auth::logout();
    $response = $this->post('/register', [
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);
    $response->assertSessionHasErrors('name');
});

test('user requires valid email format', function () {
    Auth::logout();
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'invalid-email',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);
    $response->assertSessionHasErrors('email');
});

test('password must match confirmation', function () {
    Auth::logout();
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'different-password',
    ]);
    $response->assertSessionHasErrors('password');
});

test('login requires email', function () {
    Auth::logout();
    $response = $this->post('/login', [
        'password' => 'password',
    ]);
    $response->assertSessionHasErrors('email');
});

test('login requires password', function () {
    Auth::logout();
    $response = $this->post('/login', [
        'email' => 'test@example.com',
    ]);
    $response->assertSessionHasErrors('password');
});

test('profile update requires name', function () {
    $response = $this->patch('/profile', [
        'email' => 'test@example.com',
    ]);
    $response->assertSessionHasErrors('name');
});

test('profile update requires valid email', function () {
    $response = $this->patch('/profile', [
        'name' => 'Test User',
        'email' => 'invalid',
    ]);
    $response->assertSessionHasErrors('email');
});
