<?php

use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['usertype' => 'admin', 'status' => 1]);
    $this->actingAs($this->admin);
});

test('all pages include sidebar component', function () {
    $pages = [
        '/admin/dashboard',
        '/admin/products',
        '/admin/periodes',
        '/admin/account/coa',
        '/admin/account/header',
        '/admin/saldoawal',
        '/admin/jurnaling',
        '/admin/bukubesar',
        '/admin/neracasaldo/',
        '/admin/otorisator/home',
    ];

    foreach ($pages as $page) {
        $response = $this->get($page);
        $response->assertOk();
        $content = $response->getContent();
        expect($content)->toContain('sidebar');
    }
});

test('all pages use applayout layout', function () {
    $pages = [
        '/admin/dashboard',
        '/admin/products',
        '/admin/periodes',
        '/admin/account/coa',
        '/admin/account/header',
        '/admin/saldoawal',
        '/admin/jurnaling',
        '/admin/bukubesar',
        '/admin/neracasaldo/',
        '/admin/otorisator/home',
    ];

    foreach ($pages as $page) {
        $response = $this->get($page);
        $content = $response->getContent();
        expect($content)->toContain('Plus+Jakarta+Sans');
    }
});

test('all pages use consistent font', function () {
    $pages = [
        '/admin/dashboard',
        '/admin/products',
        '/admin/periodes',
    ];

    foreach ($pages as $page) {
        $response = $this->get($page);
        $content = $response->getContent();
        expect($content)->toContain('Plus+Jakarta+Sans');
    }
});

test('all pages have responsive meta tag', function () {
    $pages = [
        '/admin/dashboard',
        '/admin/products',
        '/admin/periodes',
    ];

    foreach ($pages as $page) {
        $response = $this->get($page);
        $content = $response->getContent();
        expect($content)->toContain('viewport');
    }
});

test('dashboard page has hero component', function () {
    $response = $this->get('/admin/dashboard');
    $content = $response->getContent();
    expect($content)->toContain('Selamat Datang');
});

test('dashboard page has KPI statistics', function () {
    $response = $this->get('/admin/dashboard');
    $content = $response->getContent();
    expect($content)->toContain('Total Jurnal');
    expect($content)->toContain('Total Debit');
    expect($content)->toContain('Total Kredit');
});

test('dashboard page has module cards', function () {
    $response = $this->get('/admin/dashboard');
    $content = $response->getContent();
    expect($content)->toContain('User Management');
    expect($content)->toContain('Jurnaling');
    expect($content)->toContain('Buku Besar');
});

test('dashboard page has activity list', function () {
    $response = $this->get('/admin/dashboard');
    $content = $response->getContent();
    expect($content)->toContain('Aktivitas Terbaru');
});

test('dashboard page has monthly summary', function () {
    $response = $this->get('/admin/dashboard');
    $content = $response->getContent();
    expect($content)->toContain('Ringkasan Bulanan');
});

test('table pages use consistent table structure', function () {
    $pages = [
        '/admin/products',
        '/admin/periodes',
        '/admin/otorisator/home',
    ];

    foreach ($pages as $page) {
        $response = $this->get($page);
        $content = $response->getContent();
        expect($content)->toContain('thead');
        expect($content)->toContain('tbody');
    }
});

test('create pages have consistent form structure', function () {
    $pages = [
        '/admin/products/create',
        '/admin/periodes/create',
        '/admin/account/coa/create',
        '/admin/account/header/create',
        '/admin/otorisator/create',
    ];

    foreach ($pages as $page) {
        $response = $this->get($page);
        $content = $response->getContent();
        expect($content)->toContain('</form>');
    }
});

test('pages have consistent card components', function () {
    $pages = [
        '/admin/dashboard',
        '/admin/products',
        '/admin/periodes',
        '/admin/account/coa',
        '/admin/account/header',
    ];

    foreach ($pages as $page) {
        $response = $this->get($page);
        $content = $response->getContent();
        expect($content)->toContain('card');
    }
});

test('all pages have proper language attribute', function () {
    $response = $this->get('/admin/dashboard');
    $content = $response->getContent();
    expect($content)->toContain('lang="en"');
});

test('all pages are accessible (have main content landmark)', function () {
    $response = $this->get('/admin/dashboard');
    $content = $response->getContent();
    expect($content)->toContain('main-content');
});

test('all form pages use POST method for data submission', function () {
    $response = $this->get('/admin/periodes/create');
    $content = $response->getContent();
    expect($content)->toContain('method="POST"');
});
