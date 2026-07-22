<?php

use App\Models\Periode;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['usertype' => 'admin', 'status' => 1]);
    $this->actingAs($this->admin);
});

test('dashboard page loads successfully', function () {
    $this->get('/admin/dashboard')->assertOk();
});

test('periode list page loads', function () {
    $this->get('/admin/periodes')->assertOk();
});

test('periode create page loads', function () {
    $this->get('/admin/periodes/create')->assertOk();
});

test('can create periode', function () {
    $response = $this->post('/admin/periodes/save', [
        'nama_periode' => 'Periode Test ' . uniqid(),
        'tanggal_awal' => '2024-01-01',
        'tanggal_akhir' => '2024-12-31',
    ]);
    $response->assertSessionHasNoErrors();
    $response->assertRedirect();
});

test('can view COA list', function () {
    $this->get('/admin/account/coa')->assertOk();
});

test('can view COA create page', function () {
    $this->get('/admin/account/coa/create')->assertOk();
});

test('can view header list', function () {
    $this->get('/admin/account/header')->assertOk();
});

test('can view saldo awal page', function () {
    $this->get('/admin/saldoawal')->assertOk();
});

test('can view jurnaling pages', function () {
    $entries = ['', 'kaskeluar', 'bankmasuk', 'bankkeluar', 'memorial', 'memorialpenutup', 'create'];
    foreach ($entries as $entry) {
        $url = $entry ? "/admin/jurnaling/{$entry}" : '/admin/jurnaling';
        $this->get($url)->assertOk();
    }
});

test('can view jurnaling showing page with parameters', function () {
    $periode = Periode::first();
    if ($periode) {
        $this->get('/admin/jurnaling/showing?month=2025-01&periode_id=' . $periode->id)->assertOk();
    }
});

test('can view bukubesar page', function () {
    $this->get('/admin/bukubesar')->assertOk();
});

test('can view bukubesar filter page', function () {
    $this->get('/admin/bukubesar/filter')->assertOk();
});

test('can view neracasaldo list page', function () {
    $this->get('/admin/neracasaldo/')->assertOk();
});

test('can view otorisator pages', function () {
    $this->get('/admin/otorisator/home')->assertOk();
    $this->get('/admin/otorisator/create')->assertOk();
});

test('can view user management page', function () {
    $this->get('/admin/products')->assertOk();
});

test('can view user create page', function () {
    $this->get('/admin/products/create')->assertOk();
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
        '/admin/products',
        '/admin/products/create',
        '/admin/periodes',
        '/admin/periodes/create',
        '/admin/account/header',
        '/admin/account/header/create',
        '/admin/account/coa',
        '/admin/account/coa/create',
        '/admin/saldoawal',
        '/admin/saldoawal/create',
        '/admin/jurnaling',
        '/admin/jurnaling/kaskeluar',
        '/admin/jurnaling/bankmasuk',
        '/admin/jurnaling/bankkeluar',
        '/admin/jurnaling/memorial',
        '/admin/jurnaling/memorialpenutup',
        '/admin/jurnaling/create',
        '/admin/bukubesar',
        '/admin/bukubesar/filter',
        '/admin/neracasaldo/',
        '/admin/otorisator/home',
        '/admin/otorisator/create',
        '/profile',
    ];

    foreach ($routes as $route) {
        $response = $this->get($route);
        $response->assertStatus(200);
    }
});
