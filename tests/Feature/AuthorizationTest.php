<?php

use App\Livewire\COAWorkspace;
use App\Livewire\OtorisatorManager;
use App\Livewire\PeriodeManager;
use App\Livewire\Posting;
use App\Livewire\UserManager;
use App\Models\User;
use Livewire\Livewire;

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

test('bod cannot access master-data modules (periodes, coa, users)', function () {
    $this->actingAs($this->bod)->get('/periodes')->assertForbidden();
    $this->actingAs($this->bod)->get('/coa-workspace')->assertForbidden();
    $this->actingAs($this->bod)->get('/users')->assertForbidden();
});

test('admin can access user management', function () {
    $this->actingAs($this->admin)->get('/users')->assertOk();
    Livewire::test(UserManager::class)->assertOk();
});

test('operator cannot access user management', function () {
    $this->actingAs($this->operator)->get('/users')->assertForbidden();
});

test('bod cannot access user management', function () {
    $this->actingAs($this->bod)->get('/users')->assertForbidden();
});

test('rootsuperuser can access posting module', function () {
    $this->actingAs($this->root)->get('/posting')->assertOk();
    Livewire::test(Posting::class)->assertOk();
});

test('admin can access posting module', function () {
    $this->actingAs($this->admin)->get('/posting')->assertOk();
});

test('admin cannot access rootsuperuser-only posting route', function () {
    $this->actingAs($this->admin)->get('/rootsuperuser/posting')->assertRedirect('/');
});

test('operator cannot access posting module', function () {
    $this->actingAs($this->operator)->get('/posting')->assertForbidden();
});

test('admin can access otorisator settings', function () {
    $this->actingAs($this->admin)->get('/otorisator')->assertOk();
    Livewire::test(OtorisatorManager::class)->assertOk();
});

test('operator can access otorisator settings', function () {
    $this->actingAs($this->operator)->get('/otorisator')->assertOk();
});

test('bod cannot access otorisator settings', function () {
    $this->actingAs($this->bod)->get('/otorisator')->assertForbidden();
});

test('admin can CRUD periode', function () {
    $this->actingAs($this->admin);
    $this->get('/periodes')->assertOk();
    Livewire::test(PeriodeManager::class)->assertOk();
});

test('operator can CRUD periode', function () {
    $this->actingAs($this->operator)->get('/periodes')->assertOk();
});

test('bod cannot CRUD periode', function () {
    $this->actingAs($this->bod)->get('/periodes')->assertForbidden();
});

test('admin can CRUD COA', function () {
    $this->actingAs($this->admin);
    $this->get('/coa-workspace')->assertOk();
    Livewire::test(COAWorkspace::class)->assertOk();
});

test('operator can CRUD COA', function () {
    $this->actingAs($this->operator)->get('/coa-workspace')->assertOk();
});

test('bod cannot CRUD COA', function () {
    $this->actingAs($this->bod)->get('/coa-workspace')->assertForbidden();
});

test('admin can access jurnaling modules', function () {
    $this->actingAs($this->admin);
    $this->get('/jurnaling')->assertOk();
    $this->get('/jurnaling-list')->assertOk();
});

test('operator can access jurnaling modules', function () {
    $this->actingAs($this->operator);
    $this->get('/jurnaling')->assertOk();
    $this->get('/jurnaling-list')->assertOk();
});

test('bod can view jurnaling modules (read-only)', function () {
    $this->actingAs($this->bod);
    $this->get('/jurnaling')->assertOk();
    $this->get('/jurnaling-list')->assertOk();
});

test('bod can view laporan modules', function () {
    $this->actingAs($this->bod);
    $this->get('/bukubesar')->assertOk();
    $this->get('/neraca-saldo')->assertOk();
});

test('unauthenticated user is redirected to login', function () {
    $this->get('/dashboard')->assertRedirect('/login');
    $this->get('/admin/dashboard')->assertRedirect('/login');
    $this->get('/periodes')->assertRedirect('/login');
});
