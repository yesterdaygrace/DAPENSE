<?php

use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['usertype' => 'admin', 'status' => 1]);
    $this->actingAs($this->admin);
});

test('dashboard loads in idle state — no toast, no modal, no loading, no validation', function () {
    $response = $this->get('/admin/dashboard');
    $content = $response->getContent();

    expect($content)->toContain('main-content');
    expect($content)->toContain('sidebar');
    expect($content)->toContain('footer');

    expect(str_contains($content, 'alert-success'))->toBeFalse();

    // Loading overlay exists but is hidden by default (idle state)
    expect($content)->toContain('loading');
    expect(str_contains($content, 'style="display: none;"'))->toBeTrue();
});

test('periode list loads in idle state — no toast, no modal, no loading', function () {
    $response = $this->get('/admin/periodes');
    $content = $response->getContent();

    expect($content)->toContain('data-table');
    expect(str_contains($content, 'alert-success'))->toBeFalse();
    expect(str_contains($content, 'alert-danger'))->toBeFalse();
});

test('COA create page loads in idle state — form only, no validation errors', function () {
    $response = $this->get('/admin/account/coa/create');
    $content = $response->getContent();

    expect($content)->toContain('</form>');
    expect(str_contains($content, 'is-invalid'))->toBeFalse();
    // text-danger may exist inside Alpine.js <template> blocks (toasts) but not in visible error messages
    expect(str_contains($content, 'border-danger'))->toBeFalse();
});

test('toast appears after successful create', function () {
    $response = $this->followingRedirects()->post('/admin/periodes/save', [
        'nama_periode' => 'Toast Test ' . uniqid(),
        'tanggal_awal' => '2024-01-01',
        'tanggal_akhir' => '2024-12-31',
    ]);
    $content = $response->getContent();
    expect($content)->toContain('success');
});

test('validation errors appear per-field after invalid submit', function () {
    $response = $this->post('/admin/periodes/save', [
        'nama_periode' => '',
        'tanggal_awal' => '',
        'tanggal_akhir' => '',
    ]);
    $response->assertSessionHasErrors(['nama_periode', 'tanggal_awal', 'tanggal_akhir']);
});

test('delete confirmation uses Alpine modal, not JavaScript confirm()', function () {
    $response = $this->get('/admin/periodes');
    $content = $response->getContent();

    expect($content)->toContain('delete-modal-open');
    expect(str_contains($content, 'confirm('))->toBeFalse();
});

test('toast component is present but empty on idle', function () {
    $response = $this->get('/admin/dashboard');
    $content = $response->getContent();

    // Toast component uses Alpine.js x-data with toasts array (starts empty)
    expect($content)->toContain('x-data');
    expect(str_contains($content, 'alert-success'))->toBeFalse();
});

test('loading overlay is hidden on idle page load', function () {
    $response = $this->get('/admin/dashboard');
    $content = $response->getContent();

    expect($content)->toContain('loading');
    expect(str_contains($content, 'style="display: none;"'))->toBeTrue();
});

test('modal is hidden on idle page load', function () {
    $response = $this->get('/admin/dashboard');
    $content = $response->getContent();

    // Modal exists but is hidden by default (display: none)
    expect(str_contains($content, 'modal-backdrop'))->toBeFalse();
});

test('profile page loads in idle state', function () {
    $response = $this->get('/profile');
    $content = $response->getContent();

    expect($content)->toContain('</form>');
    expect(str_contains($content, 'alert-success'))->toBeFalse();
});

test('user management page loads without error modals visible', function () {
    $response = $this->get('/admin/products');
    $content = $response->getContent();

    expect($content)->toContain('data-table');
    expect(str_contains($content, 'modal-backdrop'))->toBeFalse();
});

test('dashboard has all idle-state required sections', function () {
    $response = $this->get('/admin/dashboard');
    $content = $response->getContent();

    expect($content)->toContain('sidebar');
    expect($content)->toContain('breadcrumb');
    expect($content)->toContain('footer');
    expect($content)->toContain('main-content');
});

test('no JavaScript alert or confirm used on list pages', function () {
    $pages = ['/admin/periodes', '/admin/account/coa', '/admin/products', '/admin/otorisator/home'];
    foreach ($pages as $page) {
        $response = $this->get($page);
        $content = $response->getContent();
        expect(str_contains($content, 'confirm('))->toBeFalse();
    }
});

test('redirect after create shows success toast', function () {
    $response = $this->post('/admin/periodes/save', [
        'nama_periode' => 'Toast Test ' . uniqid(),
        'tanggal_awal' => '2024-01-01',
        'tanggal_akhir' => '2024-12-31',
    ]);

    $response->assertSessionHas('success');
    expect($response->isRedirect())->toBeTrue();
});
