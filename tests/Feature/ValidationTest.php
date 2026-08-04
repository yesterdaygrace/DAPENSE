<?php

use App\Livewire\COAWorkspace;
use App\Livewire\OtorisatorManager;
use App\Livewire\PeriodeManager;
use App\Models\HeaderCOA;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->create(['usertype' => 'admin', 'status' => 1]);
    $this->actingAs($this->admin);
});

test('periode requires nama field', function () {
    Livewire::test(PeriodeManager::class)
        ->set('formData.nama_periode', '')
        ->set('formData.tanggal_awal', '2024-01-01')
        ->set('formData.tanggal_akhir', '2024-12-31')
        ->call('save')
        ->assertHasErrors('formData.nama_periode');
});

test('periode requires tanggal_awal field', function () {
    Livewire::test(PeriodeManager::class)
        ->set('formData.nama_periode', 'Test Periode')
        ->set('formData.tanggal_awal', '')
        ->set('formData.tanggal_akhir', '2024-12-31')
        ->call('save')
        ->assertHasErrors('formData.tanggal_awal');
});

test('periode requires tanggal_akhir field', function () {
    Livewire::test(PeriodeManager::class)
        ->set('formData.nama_periode', 'Test Periode')
        ->set('formData.tanggal_awal', '2024-01-01')
        ->set('formData.tanggal_akhir', '')
        ->call('save')
        ->assertHasErrors('formData.tanggal_akhir');
});

test('tanggal_akhir must be after tanggal_awal', function () {
    Livewire::test(PeriodeManager::class)
        ->set('formData.nama_periode', 'Test Periode')
        ->set('formData.tanggal_awal', '2024-12-31')
        ->set('formData.tanggal_akhir', '2024-01-01')
        ->call('save')
        ->assertHasErrors('formData.tanggal_akhir');
});

test('COA requires kode_akun', function () {
    $header = HeaderCOA::create([
        'kode_header' => 'H001',
        'nama_header' => 'Test Header',
        'level' => 1,
    ]);

    Livewire::test(COAWorkspace::class)
        ->set('formData.nama_akun', 'Test Akun')
        ->set('formData.saldo_normal', 'Debit')
        ->set('formData.kategori', 'Aktiva')
        ->set('formData.level', 1)
        ->set('formData.header_coa_id', $header->id)
        ->call('saveAccount')
        ->assertHasErrors('formData.kode_akun');
});

test('COA requires nama_akun', function () {
    $header = HeaderCOA::create([
        'kode_header' => 'H001',
        'nama_header' => 'Test Header',
        'level' => 1,
    ]);

    Livewire::test(COAWorkspace::class)
        ->set('formData.kode_akun', '10001')
        ->set('formData.saldo_normal', 'Debit')
        ->set('formData.kategori', 'Aktiva')
        ->set('formData.level', 1)
        ->set('formData.header_coa_id', $header->id)
        ->call('saveAccount')
        ->assertHasErrors('formData.nama_akun');
});

test('COA requires valid saldo_normal', function () {
    $header = HeaderCOA::create([
        'kode_header' => 'H001',
        'nama_header' => 'Test Header',
        'level' => 1,
    ]);

    Livewire::test(COAWorkspace::class)
        ->set('formData.kode_akun', '10001')
        ->set('formData.nama_akun', 'Test Akun')
        ->set('formData.saldo_normal', 'InvalidValue')
        ->set('formData.kategori', 'Aktiva')
        ->set('formData.level', 1)
        ->set('formData.header_coa_id', $header->id)
        ->call('saveAccount')
        ->assertHasErrors('formData.saldo_normal');
});

test('Header COA requires kode_header', function () {
    Livewire::test(COAWorkspace::class)
        ->set('headerForm.nama_header', 'Test Header')
        ->set('headerForm.level', 1)
        ->call('saveHeader')
        ->assertHasErrors('headerForm.kode_header');
});

test('Header COA requires nama_header', function () {
    Livewire::test(COAWorkspace::class)
        ->set('headerForm.kode_header', 'H001')
        ->set('headerForm.level', 1)
        ->call('saveHeader')
        ->assertHasErrors('headerForm.nama_header');
});

test('Otorisator requires nama_otorisator', function () {
    Livewire::test(OtorisatorManager::class)
        ->set('formData.nama_otorisator', '')
        ->set('formData.jabatan_otorisator', 'Manager')
        ->call('save')
        ->assertHasErrors('formData.nama_otorisator');
});

test('Otorisator requires jabatan_otorisator', function () {
    Livewire::test(OtorisatorManager::class)
        ->set('formData.nama_otorisator', 'John Doe')
        ->set('formData.jabatan_otorisator', '')
        ->call('save')
        ->assertHasErrors('formData.jabatan_otorisator');
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
