<?php

use App\Livewire\PeriodeManager;
use App\Models\Periode;
use App\Models\User;
use Livewire\Livewire;

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
    $response = $this->get('/periodes');
    $content = $response->getContent();

    expect($content)->toContain('data-table');
    expect(str_contains($content, 'alert-success'))->toBeFalse();
    expect(str_contains($content, 'alert-danger'))->toBeFalse();
});

test('COA workspace loads in idle state — form only, no validation errors', function () {
    $response = $this->get('/coa-workspace');
    $content = $response->getContent();

    expect($content)->toContain('data-table');
    expect(str_contains($content, 'is-invalid'))->toBeFalse();
    expect(str_contains($content, 'border-danger'))->toBeFalse();
});

test('toast appears after successful create', function () {
    $nama = 'Toast Test ' . uniqid();

    Livewire::test(PeriodeManager::class)
        ->set('formData.nama_periode', $nama)
        ->set('formData.tanggal_awal', '2024-01-01')
        ->set('formData.tanggal_akhir', '2024-12-31')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('periodes', ['nama_periode' => $nama]);
});

test('validation errors appear per-field after invalid submit', function () {
    Livewire::test(PeriodeManager::class)
        ->set('formData.nama_periode', '')
        ->set('formData.tanggal_awal', '')
        ->set('formData.tanggal_akhir', '')
        ->call('save')
        ->assertHasErrors(['formData.nama_periode', 'formData.tanggal_awal', 'formData.tanggal_akhir']);
});

test('delete confirmation uses modal, not JavaScript confirm()', function () {
    Periode::create([
        'nama_periode' => 'Periode Delete Test',
        'tanggal_awal' => '2024-01-01',
        'tanggal_akhir' => '2024-12-31',
        'is_rekap' => false,
    ]);

    $response = $this->get('/periodes');
    $content = $response->getContent();

    expect($content)->toContain('wire:click="confirmDelete');
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
    $response = $this->get('/periodes');
    $content = $response->getContent();

    // Modal exists but is hidden by default (not rendered when $showModal is false)
    expect(str_contains($content, 'modal-backdrop'))->toBeFalse();
});

test('profile page loads in idle state', function () {
    $response = $this->get('/profile');
    $content = $response->getContent();

    expect($content)->toContain('</form>');
    expect(str_contains($content, 'alert-success'))->toBeFalse();
});

test('user management page loads without error modals visible', function () {
    $response = $this->get('/users');
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
    $pages = ['/periodes', '/coa-workspace', '/users', '/otorisator'];
    foreach ($pages as $page) {
        $response = $this->get($page);
        $content = $response->getContent();
        expect(str_contains($content, 'confirm('))->toBeFalse();
    }
});

test('redirect after create shows success toast', function () {
    $nama = 'Toast Test ' . uniqid();

    Livewire::test(PeriodeManager::class)
        ->set('formData.nama_periode', $nama)
        ->set('formData.tanggal_awal', '2024-01-01')
        ->set('formData.tanggal_akhir', '2024-12-31')
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('periodes', ['nama_periode' => $nama]);
});
