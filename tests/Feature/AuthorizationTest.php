<?php

use App\Models\Periode;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['usertype' => 'admin', 'status' => 1]);
    $this->operator = User::factory()->create(['usertype' => 'operator', 'status' => 1]);
    $this->bod = User::factory()->create(['usertype' => 'bod', 'status' => 1]);
    $this->root = User::factory()->create(['usertype' => 'rootsuperuser', 'status' => 1]);
    $this->inactiveUser = User::factory()->create(['usertype' => 'admin', 'status' => 0]);
});

test('admin can access admin dashboard', function () {
    $this->actingAs($this->admin)->get('/admin/dashboard')->assertOk();
});

test('operator can access operator dashboard', function () {
    $this->actingAs($this->operator)->get('/operator/dashboard')->assertOk();
});

test('bod can access bod dashboard', function () {
    $this->actingAs($this->bod)->get('/bod/dashboard')->assertOk();
});

test('rootsuperuser can access rootsuperuser dashboard', function () {
    $this->actingAs($this->root)->get('/rootsuperuser/dashboard')->assertOk();
});

test('admin cannot access operator-specific dashboard route', function () {
    $this->actingAs($this->admin)->get('/operator/dashboard')->assertRedirect('/');
});

test('operator cannot access admin-specific dashboard route', function () {
    $this->actingAs($this->operator)->get('/admin/dashboard')->assertRedirect('/');
});

test('bod cannot access admin modules', function () {
    $this->actingAs($this->bod)->get('/admin/periodes')->assertRedirect('/');
    $this->actingAs($this->bod)->get('/admin/account/coa')->assertRedirect('/');
    $this->actingAs($this->bod)->get('/admin/products')->assertRedirect('/');
});

test('admin can access user management', function () {
    $this->actingAs($this->admin)->get('/admin/products')->assertOk();
});

test('operator cannot access user management', function () {
    $this->actingAs($this->operator)->get('/admin/products')->assertRedirect('/');
});

test('bod cannot access user management', function () {
    $this->actingAs($this->bod)->get('/admin/products')->assertRedirect('/');
});

test('rootsuperuser can access posting module', function () {
    $this->actingAs($this->root)->get('/rootsuperuser/posting')->assertOk();
});

test('admin cannot access posting module', function () {
    $this->actingAs($this->admin)->get('/rootsuperuser/posting')->assertRedirect('/');
});

test('admin can access otorisator settings', function () {
    $this->actingAs($this->admin)->get('/admin/otorisator/home')->assertOk();
});

test('operator can access otorisator settings', function () {
    $this->actingAs($this->operator)->get('/operator/otorisator/home')->assertOk();
});

test('bod cannot access otorisator settings', function () {
    $this->actingAs($this->bod)->get('/admin/otorisator/home')->assertRedirect('/');
});

test('admin can CRUD periode', function () {
    $this->actingAs($this->admin);
    $this->get('/admin/periodes')->assertOk();
    $this->get('/admin/periodes/create')->assertOk();
});

test('operator can CRUD periode', function () {
    $this->actingAs($this->operator);
    $this->get('/operator/periodes')->assertOk();
    $this->get('/operator/periodes/create')->assertOk();
});

test('admin can CRUD COA', function () {
    $this->actingAs($this->admin);
    $this->get('/admin/account/coa')->assertOk();
    $this->get('/admin/account/coa/create')->assertOk();
});

test('operator can CRUD COA', function () {
    $this->actingAs($this->operator);
    $this->get('/operator/account/coa')->assertOk();
    $this->get('/operator/account/coa/create')->assertOk();
});

test('admin can access jurnaling modules', function () {
    $this->actingAs($this->admin);
    $this->get('/admin/jurnaling')->assertOk();
    $this->get('/admin/jurnaling/kaskeluar')->assertOk();
    $this->get('/admin/jurnaling/bankmasuk')->assertOk();
    $this->get('/admin/jurnaling/bankkeluar')->assertOk();
    $this->get('/admin/jurnaling/memorial')->assertOk();
    $this->get('/admin/jurnaling/memorialpenutup')->assertOk();
});

test('bod can view laporan modules', function () {
    $this->actingAs($this->bod);
    $this->get('/bod/bukubesar')->assertOk();
    $this->get('/bod/neracasaldo/')->assertOk();
    $periode = Periode::first();
    if ($periode) {
        $this->get('/bod/jurnaling/showing?month=2025-01&periode_id=' . $periode->id)->assertOk();
    }
});

test('unauthenticated user is redirected to login', function () {
    $this->get('/dashboard')->assertRedirect('/login');
    $this->get('/admin/dashboard')->assertRedirect('/login');
    $this->get('/operator/periodes')->assertRedirect('/login');
});
