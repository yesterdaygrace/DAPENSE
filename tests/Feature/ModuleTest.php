<?php

use App\Livewire\COAWorkspace;
use App\Livewire\JurnalList;
use App\Livewire\JurnalManager;
use App\Livewire\OtorisatorManager;
use App\Livewire\PeriodeManager;
use App\Livewire\UserManager;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->create(['usertype' => 'admin', 'status' => 1]);
    $this->actingAs($this->admin);
});

test('dashboard page loads successfully', function () {
    $this->get('/admin/dashboard')->assertOk();
});

test('livewire dashboard page loads successfully', function () {
    $this->get('/dashboard')->assertOk();
});

test('periode list page loads', function () {
    $this->get('/periodes')->assertOk();
});

test('periode manager component renders', function () {
    Livewire::test(PeriodeManager::class)->assertOk();
});

test('can create periode via livewire', function () {
    $nama = 'Periode Test ' . uniqid();

    Livewire::test(PeriodeManager::class)
        ->set('formData.nama_periode', $nama)
        ->set('formData.tanggal_awal', '2024-01-01')
        ->set('formData.tanggal_akhir', '2024-12-31')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('periodes', ['nama_periode' => $nama]);
});

test('can view COA workspace', function () {
    $this->get('/coa-workspace')->assertOk();
});

test('COA workspace component renders', function () {
    Livewire::test(COAWorkspace::class)->assertOk();
});

test('can view saldo awal page', function () {
    $this->get('/saldo-awal')->assertOk();
});

test('can view jurnaling pages', function () {
    $this->get('/jurnaling')->assertOk();
    $this->get('/jurnaling-list')->assertOk();
});

test('jurnaling manager component renders', function () {
    Livewire::test(JurnalManager::class)->assertOk();
});

test('jurnal list component renders', function () {
    Livewire::test(JurnalList::class)->assertOk();
});

test('can view bukubesar page', function () {
    $this->get('/bukubesar')->assertOk();
});

test('can view neracasaldo page', function () {
    $this->get('/neraca-saldo')->assertOk();
});

test('can view otorisator page', function () {
    $this->get('/otorisator')->assertOk();
});

test('otorisator manager component renders', function () {
    Livewire::test(OtorisatorManager::class)->assertOk();
});

test('can view user management page', function () {
    $this->get('/users')->assertOk();
});

test('user manager component renders', function () {
    Livewire::test(UserManager::class)->assertOk();
});

test('can access profile page', function () {
    $this->get('/profile')->assertOk();
});

test('can update profile', function () {
    $response = $this->patch('/profile', [
        'name' => 'Updated Name',
        'email' => $this->admin->email,
    ]);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect('/profile');
});

test('semua admin routes return 200', function () {
    $routes = [
        '/admin/dashboard',
        '/dashboard',
        '/periodes',
        '/coa-workspace',
        '/saldo-awal',
        '/jurnaling',
        '/jurnaling-list',
        '/bukubesar',
        '/neraca-saldo',
        '/otorisator',
        '/users',
        '/profile',
    ];

    foreach ($routes as $route) {
        $response = $this->get($route);
        $response->assertStatus(200);
    }
});
