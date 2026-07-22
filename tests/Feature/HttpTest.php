<?php

use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['usertype' => 'admin', 'status' => 1]);
});

test('unauthenticated user gets 302 redirect on protected routes', function () {
    $protectedRoutes = [
        '/admin/dashboard',
        '/admin/periodes',
        '/admin/account/coa',
        '/admin/products',
        '/admin/jurnaling',
        '/admin/bukubesar',
        '/admin/neracasaldo/',
        '/profile',
    ];

    foreach ($protectedRoutes as $route) {
        $this->get($route)->assertRedirect('/login');
    }
});

test('authenticated user gets 200 on protected routes', function () {
    $this->actingAs($this->admin);

    $protectedRoutes = [
        '/admin/dashboard',
        '/admin/periodes',
        '/admin/account/coa',
        '/admin/account/header',
        '/admin/saldoawal',
        '/admin/products',
        '/admin/otorisator/home',
        '/admin/jurnaling',
        '/admin/bukubesar',
        '/admin/neracasaldo/',
        '/profile',
    ];

    foreach ($protectedRoutes as $route) {
        $response = $this->get($route);
        $response->assertStatus(200);
    }
});

test('login page returns 200', function () {
    $this->get('/login')->assertStatus(200);
});

test('register page returns 200', function () {
    $this->get('/register')->assertStatus(200);
});

test('profile page returns 200 for authenticated user', function () {
    $this->actingAs($this->admin)->get('/profile')->assertOk();
});

test('profile redirects to login for guests', function () {
    $this->get('/profile')->assertRedirect('/login');
});

test('logout redirects to home', function () {
    $this->actingAs($this->admin)->post('/logout')->assertRedirect('/');
});

test('authenticated user is redirected from login', function () {
    $this->actingAs($this->admin)->get('/login')->assertRedirect('/dashboard');
});

test('authenticated user is redirected from register', function () {
    $this->actingAs($this->admin)->get('/register')->assertRedirect('/dashboard');
});

test('login with valid credentials redirects to dashboard', function () {
    $user = User::factory()->create(['status' => 1, 'usertype' => 'admin']);
    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);
    $response->assertRedirect('/admin/dashboard');
});

test('login with invalid credentials returns error', function () {
    $user = User::factory()->create(['status' => 1, 'usertype' => 'admin']);
    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrongpassword',
    ]);
    $response->assertSessionHasErrors();
});

test('profile PATCH returns 302 redirect', function () {
    $this->actingAs($this->admin)
        ->patch('/profile', ['name' => 'Test', 'email' => $this->admin->email])
        ->assertRedirect('/profile');
});

test('profile DELETE with wrong password returns error', function () {
    $this->actingAs($this->admin)
        ->delete('/profile', ['password' => 'wrong'])
        ->assertSessionHasErrorsIn('userDeletion', 'password');
});

test('POST to profile without auth returns 302', function () {
    $this->patch('/profile', ['name' => 'Test', 'email' => 'test@test.com'])
        ->assertRedirect('/login');
});

test('role middleware blocks wrong role', function () {
    $user = User::factory()->create(['usertype' => 'bod', 'status' => 1]);
    $this->actingAs($user)->get('/admin/products')->assertRedirect('/');
});

test('COA edit page returns 404 for non-existent COA', function () {
    $this->actingAs($this->admin)->get('/admin/account/coa/edit/99999')->assertNotFound();
});

test('periode edit page returns 404 for non-existent periode', function () {
    $this->actingAs($this->admin)->get('/admin/periodes/edit/99999')->assertNotFound();
});

test('BOD routes are accessible by BOD user', function () {
    $bod = User::factory()->create(['usertype' => 'bod', 'status' => 1]);
    $this->actingAs($bod);
    $this->get('/bod/dashboard')->assertOk();
    $this->get('/bod/bukubesar')->assertOk();
    $this->get('/bod/neracasaldo/')->assertOk();
});

test('operator routes are accessible by operator user', function () {
    $operator = User::factory()->create(['usertype' => 'operator', 'status' => 1]);
    $this->actingAs($operator);
    $this->get('/operator/dashboard')->assertOk();
    $this->get('/operator/periodes')->assertOk();
    $this->get('/operator/jurnaling')->assertOk();
    $this->get('/operator/bukubesar')->assertOk();
});

test('rootsuperuser routes are accessible by rootsuperuser', function () {
    $root = User::factory()->create(['usertype' => 'rootsuperuser', 'status' => 1]);
    $this->actingAs($root);
    $this->get('/rootsuperuser/dashboard')->assertOk();
    $this->get('/rootsuperuser/posting')->assertOk();
    $this->get('/rootsuperuser/products')->assertOk();
});
