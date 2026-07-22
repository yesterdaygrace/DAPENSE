<?php

use App\Http\Controllers\admin\ProductControllerAdmin;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Base\BukuBesarController;
use App\Http\Controllers\Base\CoaController;
use App\Http\Controllers\Base\HeaderController;
use App\Http\Controllers\Base\JurnalingController;
use App\Http\Controllers\Base\NeracaSaldoController;
use App\Http\Controllers\Base\OtorisatorController;
use App\Http\Controllers\Base\PeriodeController;
use App\Http\Controllers\Base\SaldoAwalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Modules\COAWorkspaceController;
use App\Http\Controllers\Modules\JournalEntryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\rootsuperuser\PostingControllerRootSuperuser;
use App\Http\Controllers\rootsuperuser\ProductControllerRootSuperuser;
use App\Livewire\BukuBesar;
use App\Livewire\COAWorkspace;
use App\Livewire\Dashboard;
use App\Livewire\JournalEntry;
use App\Livewire\JurnalList;
use App\Livewire\JurnalManager;
use App\Livewire\NeracaSaldo;
use App\Livewire\OtorisatorManager;
use App\Livewire\PeriodeManager;
use App\Livewire\Posting;
use App\Livewire\SaldoAwal;
use App\Livewire\UserManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('auth.login');
});

/*
|--------------------------------------------------------------------------
| Livewire App Routes (NEW — single set, role-aware components)
|--------------------------------------------------------------------------
| Each route maps to a Livewire full-page component. Components check
| the user's role internally via the HasRole trait.
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/logout', [AuthenticatedSessionController::class, 'logout'])->name('logout.get');

    // Livewire full-page components (role-aware — no role prefix needed)
    Route::get('/dashboard', Dashboard::class)->name('dashboard')->middleware('no-cache');
    Route::view('/master-data', 'modules.master-data.index')->name('master-data');
    Route::get('/coa-workspace', COAWorkspace::class)->name('coa-workspace');
    Route::post('/coa-workspace/export', [COAWorkspaceController::class, 'exportData'])->name('coa-workspace.export');
    Route::post('/coa-workspace/import', [COAWorkspaceController::class, 'importStore'])->name('coa-workspace.import');
    Route::get('/coa-workspace/template', [COAWorkspaceController::class, 'downloadTemplate'])->name('coa-workspace.template');
    Route::view('/transactions', 'modules.transactions.index')->name('transactions');
    Route::get('/jurnal-entry', JournalEntry::class)->name('jurnal-entry');
    Route::post('/jurnal-entry', [JournalEntryController::class, 'store'])->name('jurnal-entry.store');
    Route::get('/jurnaling', JurnalManager::class)->name('jurnaling');
    Route::get('/jurnaling-list', JurnalList::class)->name('jurnaling-list');
    Route::get('/jurnaling/export', [JurnalingController::class, 'exportJurnaling'])->name('jurnaling.export');
    Route::view('/reports', 'modules.reports.index')->name('reports');
    Route::get('/bukubesar', BukuBesar::class)->name('bukubesar');
    Route::get('/bukubesar/export', [BukuBesarController::class, 'exportExcel'])->name('bukubesar.export');
    Route::get('/neraca-saldo/{periode?}', NeracaSaldo::class)->name('neraca-saldo');
    Route::get('/neraca-saldo/exportexcel/{periode_id}', [NeracaSaldoController::class, 'exportExcel'])->name('neraca-saldo.exportexcel');
    Route::get('/neraca-saldo/exportpdf/{periode_id}', [NeracaSaldoController::class, 'exportPdf'])->name('neraca-saldo.exportpdf');
    Route::view('/finance', 'modules.finance.index')->name('finance');
    Route::get('/saldo-awal', SaldoAwal::class)->name('saldo-awal');
    Route::get('/periodes', PeriodeManager::class)->name('periodes');
    Route::get('/otorisator', OtorisatorManager::class)->name('otorisator');
    Route::view('/administration', 'modules.administration.index')->name('administration');
    Route::get('/users', UserManager::class)->name('users');
    Route::get('/posting', Posting::class)->name('posting');
    Route::post('/posting', [PostingControllerRootSuperuser::class, 'postJurnal'])->name('posting.post');
    Route::view('/settings', 'modules.settings.index')->name('settings');
});

/* ===================================================================
   LEGACY ROUTES — kept for backward compatibility with existing
   controller-based views and non-Livewire pages (product CRUD, etc.)
   =================================================================== */
Route::middleware(['auth', 'role:rootsuperuser'])->prefix('rootsuperuser')->name('rootsuperuser/')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'homerootsuperuser'])->name('dashboard');
    Route::view('/master-data', 'modules.master-data.index')->name('master-data');
    Route::view('/transactions', 'modules.transactions.index')->name('transactions');
    Route::view('/reports', 'modules.reports.index')->name('reports');
    Route::view('/finance', 'modules.finance.index')->name('finance');
    Route::view('/administration', 'modules.administration.index')->name('administration');
    Route::view('/settings', 'modules.settings.index')->name('settings');
    Route::get('/master-data/coa-workspace', [COAWorkspaceController::class, 'index'])->name('master-data/coa-workspace');
    Route::post('/master-data/coa-workspace/export', [COAWorkspaceController::class, 'exportData'])->name('master-data/coa-workspace.export');
    Route::post('/master-data/coa-workspace/import', [COAWorkspaceController::class, 'importStore'])->name('master-data/coa-workspace.import');
    Route::get('/master-data/coa-workspace/template', [COAWorkspaceController::class, 'downloadTemplate'])->name('master-data/coa-workspace.template');
    Route::get('/transactions/journal-entry', [JournalEntryController::class, 'index'])->name('transactions/journal-entry');
    Route::post('/transactions/journal-entry', [JournalEntryController::class, 'store'])->name('transactions/journal-entry.store');

    Route::get('/products', [ProductControllerRootSuperuser::class, 'index'])->name('products');
    Route::get('/products/create', [ProductControllerRootSuperuser::class, 'create'])->name('products/create');
    Route::post('/products/save', [ProductControllerRootSuperuser::class, 'save'])->name('products/save');
    Route::get('/products/edit/{id}', [ProductControllerRootSuperuser::class, 'edit'])->name('products/edit');
    Route::put('/products/update/{id}', [ProductControllerRootSuperuser::class, 'update'])->name('products/update');
    Route::get('/products/delete/{id}', [ProductControllerRootSuperuser::class, 'delete'])->name('products/delete');
    Route::get('/products/status/{id}', [ProductControllerRootSuperuser::class, 'toggleStatus'])->name('products/status');

    Route::get('/account/header', [HeaderController::class, 'index'])->name('account/header');
    Route::get('/account/header/create', [HeaderController::class, 'create'])->name('account/header/create');
    Route::post('/account/header/save', [HeaderController::class, 'save'])->name('account/header/save');
    Route::get('/account/header/edit/{id}', [HeaderController::class, 'update'])->name('account/header/edit');
    Route::put('/account/header/update/{id}', [HeaderController::class, 'updatesave'])->name('account/header/update');
    Route::get('/account/header/delete/{id}', [HeaderController::class, 'delete'])->name('account/header/delete');

    Route::get('/account/coa', [CoaController::class, 'index'])->name('account/coa');
    Route::get('/account/coa/create', [CoaController::class, 'create'])->name('account/coa/create');
    Route::post('/account/coa/save', [CoaController::class, 'save'])->name('account/coa/save');
    Route::get('/account/coa/edit/{id}', [CoaController::class, 'update'])->name('account/coa/edit');
    Route::put('/account/coa/update/{id}', [CoaController::class, 'updatesave'])->name('account/coa/update');
    Route::get('/account/coa/delete/{id}', [CoaController::class, 'delete'])->name('account/coa/delete');

    Route::get('/periodes', [PeriodeController::class, 'index'])->name('periodes');
    Route::get('/periodes/create', [PeriodeController::class, 'create'])->name('periodes/create');
    Route::post('/periodes/save', [PeriodeController::class, 'save'])->name('periodes/save');
    Route::get('/periodes/edit/{id}', [PeriodeController::class, 'update'])->name('periodes/edit');
    Route::put('/periodes/update/{id}', [PeriodeController::class, 'updatesave'])->name('periodes/update');
    Route::get('/periodes/delete/{id}', [PeriodeController::class, 'delete'])->name('periodes/delete');

    Route::get('/jurnaling', [JurnalingController::class, 'index'])->name('jurnaling');
    Route::get('/jurnaling/kaskeluar', [JurnalingController::class, 'indexkaskeluar'])->name('jurnaling/kaskeluar');
    Route::get('/jurnaling/bankmasuk', [JurnalingController::class, 'indexbankmasuk'])->name('jurnaling/bankmasuk');
    Route::get('/jurnaling/bankkeluar', [JurnalingController::class, 'indexbankkeluar'])->name('jurnaling/bankkeluar');
    Route::get('/jurnaling/memorial', [JurnalingController::class, 'indexmemorial'])->name('jurnaling/memorial');
    Route::get('/jurnaling/memorialpenutup', [JurnalingController::class, 'indexmemorialpenutup'])->name('jurnaling/memorialpenutup');
    Route::get('/jurnaling/create', [JurnalingController::class, 'create'])->name('jurnaling/create');
    Route::post('/jurnaling/save', [JurnalingController::class, 'save'])->name('jurnaling/save');
    Route::post('/jurnaling/store', [JurnalingController::class, 'store'])->name('jurnaling/store');
    Route::post('/jurnaling/storekaskeluar', [JurnalingController::class, 'storekaskeluar'])->name('jurnaling/storekaskeluar');
    Route::post('/jurnaling/storebankmasuk', [JurnalingController::class, 'storebankmasuk'])->name('jurnaling/storebankmasuk');
    Route::post('/jurnaling/storebankkeluar', [JurnalingController::class, 'storebankkeluar'])->name('jurnaling/storebankkeluar');
    Route::post('/jurnaling/storememorial', [JurnalingController::class, 'storememorial'])->name('jurnaling/storememorial');
    Route::post('/jurnaling/storememorialpenutup', [JurnalingController::class, 'storememorialpenutup'])->name('jurnaling/storememorialpenutup');
    Route::post('/jurnaling/unrekap/{periode_id}', [JurnalingController::class, 'unrekapJurnal'])->name('jurnaling/unrekap');
    Route::post('/jurnaling/rekap/{periode_id}', [JurnalingController::class, 'rekapJurnal'])->name('jurnaling/rekap');
    Route::get('/jurnaling/showing', [JurnalingController::class, 'showEntries'])->name('jurnaling/showing');
    Route::get('/jurnaling/months', [JurnalingController::class, 'showMonths'])->name('jurnaling/months');
    Route::get('/jurnaling/export', [JurnalingController::class, 'exportJurnaling'])->name('jurnaling/export');
    Route::get('/jurnaling/cek-nomor-buktikm', [JurnalingController::class, 'cekNomorBuktiKM'])->name('cekNomorBuktiKM');
    Route::put('/jurnaling/editkm/{id}', [JurnalingController::class, 'updatekm'])->name('jurnaling/updatekm');

    Route::get('/bukubesar', [BukuBesarController::class, 'showLedgerForm'])->name('bukubesar');

    Route::get('/saldoawal', [SaldoAwalController::class, 'index'])->name('saldoawal');
    Route::get('/saldoawal/create', [SaldoAwalController::class, 'create'])->name('saldoawal/create');
    Route::post('/saldoawal/store', [SaldoAwalController::class, 'store'])->name('saldoawal/store');

    Route::get('/posting', [PostingControllerRootSuperuser::class, 'index'])->name('posting');
    Route::post('/posting', [PostingControllerRootSuperuser::class, 'postJurnal'])->name('posting/post');

    Route::get('/neracasaldo/{periode_id}', [NeracaSaldoController::class, 'index'])->name('neracasaldo');
    Route::get('/neracasaldo/', [NeracaSaldoController::class, 'indexrecap'])->name('neracasaldo/');
    Route::get('/neracasaldo/showing/{periode_id}', [NeracaSaldoController::class, 'indexmon'])->name('neracasaldo/showing');
    Route::get('/neracasaldo/months/{periode?}', [JurnalingController::class, 'showPerMonth'])->name('neracasaldo/months');
    Route::get('/neracasaldo/monthstampil/{periode?}', [NeracaSaldoController::class, 'showPerMonthNeraca'])->name('neracasaldo/monthstampil');
    Route::get('/neracasaldo/rekap/{periode_id}', [JurnalingController::class, 'rekapJurnalMonth'])->name('neracasaldo/rekap');
    Route::get('/neracasaldo/exportexcel/{periode_id}', [NeracaSaldoController::class, 'exportExcel'])->name('neracasaldo/exportexcel');
    Route::get('/neracasaldo/exportpdf/{periode_id}', [NeracaSaldoController::class, 'exportPdf'])->name('neracasaldo/exportpdf');

    Route::get('/otorisator/home', [OtorisatorController::class, 'index'])->name('otorisator/home');
    Route::get('/otorisator/create', [OtorisatorController::class, 'create'])->name('otorisator/create');
    Route::post('/otorisator/save', [OtorisatorController::class, 'store'])->name('otorisator/save');
    Route::get('/otorisator/edit/{id}', [OtorisatorController::class, 'edit'])->name('otorisator/edit');
    Route::put('/otorisator/update/{id}', [OtorisatorController::class, 'update'])->name('otorisator/update');
    Route::delete('/otorisator/delete/{id}', [OtorisatorController::class, 'destroy'])->name('otorisator/delete');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin/')->group(function () {
    Route::view('/dashboard', 'dashboard.index')->name('dashboard');
    Route::view('/master-data', 'modules.master-data.index')->name('master-data');
    Route::view('/transactions', 'modules.transactions.index')->name('transactions');
    Route::view('/reports', 'modules.reports.index')->name('reports');
    Route::view('/finance', 'modules.finance.index')->name('finance');
    Route::view('/administration', 'modules.administration.index')->name('administration');
    Route::view('/settings', 'modules.settings.index')->name('settings');

    Route::get('/products', [ProductControllerAdmin::class, 'index'])->name('products');
    Route::get('/products/create', [ProductControllerAdmin::class, 'create'])->name('products/create');
    Route::post('/products/save', [ProductControllerAdmin::class, 'save'])->name('products/save');
    Route::get('/products/edit/{id}', [ProductControllerAdmin::class, 'edit'])->name('products/edit');
    Route::put('/products/update/{id}', [ProductControllerAdmin::class, 'update'])->name('products/update');
    Route::get('/products/delete/{id}', [ProductControllerAdmin::class, 'delete'])->name('products/delete');
    Route::get('/products/status/{id}', [ProductControllerAdmin::class, 'toggleStatus'])->name('products/status');

    Route::get('/account/header', [HeaderController::class, 'index'])->name('account/header');
    Route::get('/account/coa', [CoaController::class, 'index'])->name('account/coa');
    Route::get('/periodes', [PeriodeController::class, 'index'])->name('periodes');
    Route::get('/jurnaling', [JurnalingController::class, 'index'])->name('jurnaling');
    Route::get('/jurnaling/showing', [JurnalingController::class, 'showEntries'])->name('jurnaling/showing');
    Route::get('/jurnaling/export', [JurnalingController::class, 'exportJurnaling'])->name('jurnaling/export');
    Route::get('/bukubesar', [BukuBesarController::class, 'showLedgerForm'])->name('bukubesar');
    Route::get('/saldoawal', [SaldoAwalController::class, 'index'])->name('saldoawal');
    Route::get('/neracasaldo/{periode_id}', [NeracaSaldoController::class, 'index'])->name('neracasaldo');
    Route::get('/neracasaldo/', [NeracaSaldoController::class, 'indexrecap'])->name('neracasaldo/');
    Route::get('/neracasaldo/exportexcel/{periode_id}', [NeracaSaldoController::class, 'exportExcel'])->name('neracasaldo/exportexcel');
    Route::get('/neracasaldo/exportpdf/{periode_id}', [NeracaSaldoController::class, 'exportPdf'])->name('neracasaldo/exportpdf');
    Route::get('/otorisator/home', [OtorisatorController::class, 'index'])->name('otorisator/home');
});

Route::middleware(['auth', 'role:operator'])->prefix('operator')->name('operator/')->group(function () {
    Route::view('/dashboard', 'dashboard.index')->name('dashboard');
    Route::view('/master-data', 'modules.master-data.index')->name('master-data');
    Route::view('/transactions', 'modules.transactions.index')->name('transactions');
    Route::view('/reports', 'modules.reports.index')->name('reports');
    Route::view('/finance', 'modules.finance.index')->name('finance');
    Route::view('/administration', 'modules.administration.index')->name('administration');
    Route::view('/settings', 'modules.settings.index')->name('settings');

    Route::get('/account/header', [HeaderController::class, 'index'])->name('account/header');
    Route::get('/account/coa', [CoaController::class, 'index'])->name('account/coa');
    Route::get('/periodes', [PeriodeController::class, 'index'])->name('periodes');
    Route::get('/jurnaling', [JurnalingController::class, 'index'])->name('jurnaling');
    Route::get('/jurnaling/showing', [JurnalingController::class, 'showEntries'])->name('jurnaling/showing');
    Route::get('/jurnaling/export', [JurnalingController::class, 'exportJurnaling'])->name('jurnaling/export');
    Route::get('/bukubesar', [BukuBesarController::class, 'showLedgerForm'])->name('bukubesar');
    Route::get('/saldoawal', [SaldoAwalController::class, 'index'])->name('saldoawal');
    Route::get('/neracasaldo/{periode_id}', [NeracaSaldoController::class, 'index'])->name('neracasaldo');
    Route::get('/neracasaldo/', [NeracaSaldoController::class, 'indexrecap'])->name('neracasaldo/');
    Route::get('/neracasaldo/exportexcel/{periode_id}', [NeracaSaldoController::class, 'exportExcel'])->name('neracasaldo/exportexcel');
    Route::get('/neracasaldo/exportpdf/{periode_id}', [NeracaSaldoController::class, 'exportPdf'])->name('neracasaldo/exportpdf');
    Route::get('/otorisator/home', [OtorisatorController::class, 'index'])->name('otorisator/home');
});

Route::middleware(['auth', 'role:bod'])->prefix('bod')->name('bod/')->group(function () {
    Route::view('/dashboard', 'dashboard.index')->name('dashboard');
    Route::view('/master-data', 'modules.master-data.index')->name('master-data');
    Route::view('/transactions', 'modules.transactions.index')->name('transactions');
    Route::view('/reports', 'modules.reports.index')->name('reports');
    Route::view('/finance', 'modules.finance.index')->name('finance');
    Route::view('/administration', 'modules.administration.index')->name('administration');
    Route::view('/settings', 'modules.settings.index')->name('settings');

    Route::get('/jurnaling/showing', [JurnalingController::class, 'showEntries'])->name('jurnaling/showing');
    Route::get('/jurnaling/export', [JurnalingController::class, 'exportJurnaling'])->name('jurnaling/export');
    Route::get('/bukubesar', [BukuBesarController::class, 'showLedgerForm'])->name('bukubesar');
    Route::get('/neracasaldo/{periode_id}', [NeracaSaldoController::class, 'index'])->name('neracasaldo');
    Route::get('/neracasaldo/', [NeracaSaldoController::class, 'indexrecap'])->name('neracasaldo/');
    Route::get('/neracasaldo/exportexcel/{periode_id}', [NeracaSaldoController::class, 'exportExcel'])->name('neracasaldo/exportexcel');
    Route::get('/neracasaldo/exportpdf/{periode_id}', [NeracaSaldoController::class, 'exportPdf'])->name('neracasaldo/exportpdf');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('admin/dashboard', [HomeController::class, 'index'])->name('admin/dashboard');

    // ERP-style modules
    Route::view('admin/master-data', 'modules.master-data.index')->name('admin/master-data');
    Route::view('admin/transactions', 'modules.transactions.index')->name('admin/transactions');
    Route::view('admin/reports', 'modules.reports.index')->name('admin/reports');
    Route::view('admin/finance', 'modules.finance.index')->name('admin/finance');
    Route::view('admin/administration', 'modules.administration.index')->name('admin/administration');
    Route::view('admin/settings', 'modules.settings.index')->name('admin/settings');
    Route::get('admin/master-data/coa-workspace', [COAWorkspaceController::class, 'index'])->name('admin/master-data/coa-workspace');
    Route::post('admin/master-data/coa-workspace/export', [COAWorkspaceController::class, 'exportData'])->name('admin/master-data/coa-workspace.export');
    Route::post('admin/master-data/coa-workspace/import', [COAWorkspaceController::class, 'importStore'])->name('admin/master-data/coa-workspace.import');
    Route::get('admin/master-data/coa-workspace/template', [COAWorkspaceController::class, 'downloadTemplate'])->name('admin/master-data/coa-workspace.template');
    Route::get('admin/transactions/journal-entry', [JournalEntryController::class, 'index'])->name('admin/transactions/journal-entry');
    Route::post('admin/transactions/journal-entry', [JournalEntryController::class, 'store'])->name('admin/transactions/journal-entry.store');

    // Legacy routes
    Route::get('/admin/products', [ProductControllerAdmin::class, 'index'])->name('admin/products');
    Route::get('/admin/products/create', [ProductControllerAdmin::class, 'create'])->name('admin/products/create');
    Route::post('/admin/products/save', [ProductControllerAdmin::class, 'save'])->name('admin/products/save');
    Route::get('/admin/products/edit/{id}', [ProductControllerAdmin::class, 'edit'])->name('admin/products/edit');
    Route::put('/admin/products/update/{id}', [ProductControllerAdmin::class, 'update'])->name('admin/products/update');
    Route::get('/admin/products/delete/{id}', [ProductControllerAdmin::class, 'delete'])->name('admin/products/delete');
    Route::get('/admin/products/status/{id}', [ProductControllerAdmin::class, 'toggleStatus'])->name('admin/products/status');

    Route::get('/admin/periodes', [PeriodeController::class, 'index'])->name('admin/periodes');
    Route::get('/admin/periodes/create', [PeriodeController::class, 'create'])->name('admin/periodes/create');
    Route::post('/admin/periodes/save', [PeriodeController::class, 'save'])->name('admin/periodes/save');
    Route::get('/admin/periodes/edit/{id}', [PeriodeController::class, 'update'])->name('admin/periodes/edit');
    Route::put('/admin/periodes/update/{id}', [PeriodeController::class, 'updatesave'])->name('admin/periodes/update');
    Route::get('/admin/periodes/delete/{id}', [PeriodeController::class, 'delete'])->name('admin/periodes/delete');

    Route::get('/admin/account/header', [HeaderController::class, 'index'])->name('admin/account/header');
    Route::get('/admin/account/header/create', [HeaderController::class, 'create'])->name('admin/account/header/create');
    Route::post('/admin/account/header/save', [HeaderController::class, 'save'])->name('admin/account/header/save');
    Route::get('/admin/account/header/edit/{id}', [HeaderController::class, 'update'])->name('admin/account/header/edit');
    Route::put('/admin/account/header/update/{id}', [HeaderController::class, 'updatesave'])->name('admin/account/header/update');
    Route::get('/admin/account/header/delete/{id}', [HeaderController::class, 'delete'])->name('admin/account/header/delete');

    Route::get('/admin/account/coa', [CoaController::class, 'index'])->name('admin/account/coa');
    Route::get('/admin/account/coa/create', [CoaController::class, 'create'])->name('admin/account/coa/create');
    Route::post('/admin/account/coa/save', [CoaController::class, 'save'])->name('admin/account/coa/save');
    Route::get('/admin/account/coa/edit/{id}', [CoaController::class, 'update'])->name('admin/account/coa/edit');
    Route::put('/admin/account/coa/update/{id}', [CoaController::class, 'updatesave'])->name('admin/account/coa/update');
    Route::get('/admin/account/coa/delete/{id}', [CoaController::class, 'delete'])->name('admin/account/coa/delete');


    Route::get('/admin/jurnaling', [JurnalingController::class, 'index'])->name('admin/jurnaling');
    Route::get('/admin/jurnaling/kaskeluar', [JurnalingController::class, 'indexkaskeluar'])->name('admin/jurnaling/kaskeluar');
    Route::get('/admin/jurnaling/bankmasuk', [JurnalingController::class, 'indexbankmasuk'])->name('admin/jurnaling/bankmasuk');
    Route::get('/admin/jurnaling/bankkeluar', [JurnalingController::class, 'indexbankkeluar'])->name('admin/jurnaling/bankkeluar');
    Route::get('/admin/jurnaling/memorial', [JurnalingController::class, 'indexmemorial'])->name('admin/jurnaling/memorial');
    Route::get('/admin/jurnaling/memorialpenutup', [JurnalingController::class, 'indexmemorialpenutup'])->name('admin/jurnaling/memorialpenutup');
    Route::get('/admin/jurnaling/create', [JurnalingController::class, 'create'])->name('admin/jurnaling/create');
    Route::post('/admin/jurnaling/save', [JurnalingController::class, 'save'])->name('admin/jurnaling/save');
    Route::post('/admin/jurnaling/store', [JurnalingController::class, 'store'])->name('admin/jurnaling/store');
    Route::post('/admin/jurnaling/storekaskeluar', [JurnalingController::class, 'storekaskeluar'])->name('admin/jurnaling/storekaskeluar');
    Route::post('/admin/jurnaling/storebankmasuk', [JurnalingController::class, 'storebankmasuk'])->name('admin/jurnaling/storebankmasuk');
    Route::post('/admin/jurnaling/storebankkeluar', [JurnalingController::class, 'storebankkeluar'])->name('admin/jurnaling/storebankkeluar');
    Route::post('/admin/jurnaling/storememorial', [JurnalingController::class, 'storememorial'])->name('admin/jurnaling/storememorial');
    Route::post('/admin/jurnaling/storememorialpenutup', [JurnalingController::class, 'storememorialpenutup'])->name('admin/jurnaling/storememorialpenutup');
    Route::post('admin/jurnaling/unrekap/{periode_id}', [JurnalingController::class, 'unrekapJurnal'])->name('admin/jurnaling/unrekap');
    Route::post('admin/jurnaling/rekap/{periode_id}', [JurnalingController::class, 'rekapJurnal'])->name('admin/jurnaling/rekap');
    Route::get('/admin/jurnaling/showing', [JurnalingController::class, 'showEntries'])->name('admin/jurnaling/showing');
    Route::get('/admin/jurnaling/months', [JurnalingController::class, 'showMonths'])->name('admin/jurnaling/months');
    Route::get('/admin/jurnaling/export', [JurnalingController::class, 'exportJurnaling'])->name('admin/jurnaling/export');

    Route::get('/admin/jurnaling/cek-nomor-buktikm', [JurnalingController::class, 'cekNomorBuktiKM'])->name('admin/cekNomorBuktiKM');
    Route::get('/admin/jurnaling/cek-nomor-buktikk', [JurnalingController::class, 'cekNomorBuktiKK'])->name('admin/cekNomorBuktiKK');
    Route::get('/admin/jurnaling/cek-nomor-buktibm', [JurnalingController::class, 'cekNomorBuktiBM'])->name('admin/cekNomorBuktiBM');
    Route::get('/admin/jurnaling/cek-nomor-buktibk', [JurnalingController::class, 'cekNomorBuktiBK'])->name('admin/cekNomorBuktiBK');
    Route::get('/admin/jurnaling/cek-nomor-buktimem', [JurnalingController::class, 'cekNomorBuktiMem'])->name('admin/cekNomorBuktiMem');
    Route::get('/admin/jurnaling/cek-nomor-buktimempenutup', [JurnalingController::class, 'cekNomorBuktiMemPenutup'])->name('admin/cekNomorBuktiMemPenutup');
    Route::put('/admin/jurnaling/editkm/{id}', [JurnalingController::class, 'updatekm'])->name('admin/jurnaling/updatekm');
    Route::put('/admin/jurnaling/editkk/{id}', [JurnalingController::class, 'updatekk'])->name('admin/jurnaling/updatekk');
    Route::put('/admin/jurnaling/editbm/{id}', [JurnalingController::class, 'updatebm'])->name('admin/jurnaling/updatebm');
    Route::put('/admin/jurnaling/editbk/{id}', [JurnalingController::class, 'updatebk'])->name('admin/jurnaling/updatebk');
    Route::put('/admin/jurnaling/editmem/{id}', [JurnalingController::class, 'updatemem'])->name('admin/jurnaling/updatemem');
    Route::put('/admin/jurnaling/editmempenutup/{id}', [JurnalingController::class, 'updatemempenutup'])->name('admin/jurnaling/updatemempenutup');

    Route::delete('/admin/jurnaling/deletekm/{id}', [JurnalingController::class, 'deletekm'])->name('admin/jurnaling/deletekm');
    Route::delete('/admin/jurnaling/deletekk/{id}', [JurnalingController::class, 'deletekk'])->name('admin/jurnaling/deletekk');
    Route::delete('/admin/jurnaling/deletebk/{id}', [JurnalingController::class, 'deletebk'])->name('admin/jurnaling/deletebk');
    Route::delete('/admin/jurnaling/deletebm/{id}', [JurnalingController::class, 'deletebm'])->name('admin/jurnaling/deletebm');
    Route::delete('/admin/jurnaling/deletemem/{id}', [JurnalingController::class, 'deletemem'])->name('admin/jurnaling/deletemem');
    Route::delete('/admin/jurnaling/deletemempenutup/{id}', [JurnalingController::class, 'deletemempenutup'])->name('admin/jurnaling/deletemempenutup');

    Route::get('/admin/bukubesar', [BukuBesarController::class, 'showLedgerForm'])->name('admin/bukubesar');
    Route::get('/admin/bukubesar/searchCoaByPeriod', [BukuBesarController::class, 'searchCoaByPeriod'])->name('admin/bukubesar/searchCoaByPeriod');
    Route::get('admin/bukubesar/showAll', [BukuBesarController::class, 'showAll'])->name('admin/bukubesar/showAll');
    Route::get('/admin/bukubesar/searchCoaByFilter', [BukuBesarController::class, 'searchCoaByFilter'])->name('admin/bukubesar/searchCoaByFilter');
    Route::get('/admin/bukubesar/searchByDate', [BukuBesarController::class, 'searchByDate'])->name('admin/bukubesar/searchByDate');
    Route::get('/admin/bukubesar/filter', [BukuBesarController::class, 'filterView'])->name('admin/bukubesar/filter');
    Route::get('admin/bukubesar/export', [BukuBesarController::class, 'exportExcel'])->name('admin/bukubesar/export');

    Route::get('/admin/saldoawal', [SaldoAwalController::class, 'index'])->name('admin/saldoawal');
    Route::get('/admin/saldoawal/create', [SaldoAwalController::class, 'create'])->name('admin/saldoawal/create');
    Route::post('/admin/saldoawal/store', [SaldoAwalController::class, 'store'])->name('admin/saldoawal/store');
    Route::get('/admin/saldoawal/{id}/edit', [SaldoAwalController::class, 'edit'])->name('admin.saldoawal.edit');
    Route::put('/admin/saldoawal/{id}', [SaldoAwalController::class, 'update'])->name('admin.saldoawal.update');
    Route::delete('/admin/saldoawal/{id}', [SaldoAwalController::class, 'destroy'])->name('admin.saldoawal.destroy');

    Route::get('/admin/neracasaldo/{periode_id}', [NeracaSaldoController::class, 'index'])->name('admin/neracasaldo');
    Route::get('/admin/neracasaldo/', [NeracaSaldoController::class, 'indexrecap'])->name('admin/neracasaldo/');
    Route::get('/admin/neracasaldo/showing/{periode_id}', [NeracaSaldoController::class, 'indexmon'])->name('admin/neracasaldo/showing');
    Route::get('/admin/neracasaldo/months/{periode?}', [JurnalingController::class, 'showPerMonth'])->name('admin/neracasaldo/months');
    Route::get('/admin/neracasaldo/monthstampil/{periode?}', [NeracaSaldoController::class, 'showPerMonthNeraca'])->name('admin/neracasaldo/monthstampil');
    Route::get('/admin/neracasaldo/rekap/{periode_id}', [JurnalingController::class, 'rekapJurnalMonth'])->name('admin/neracasaldo/rekap');
    Route::get('/admin/neracasaldo/exportexcel/{periode_id}', [NeracaSaldoController::class, 'exportExcel'])->name('admin/neracasaldo/exportexcel');
    Route::get('/admin/neracasaldo/exportpdf/{periode_id}', [NeracaSaldoController::class, 'exportPdf'])->name('admin/neracasaldo/exportpdf');

    Route::get('/admin/otorisator/home', [OtorisatorController::class, 'index'])->name('admin/otorisator/home');
    Route::get('/admin/otorisator/create', [OtorisatorController::class, 'create'])->name('admin/otorisator/create');
    Route::post('/admin/otorisator/save', [OtorisatorController::class, 'store'])->name('admin/otorisator/save');
    Route::get('/admin/otorisator/edit/{id}', [OtorisatorController::class, 'edit'])->name('admin/otorisator/edit');
    Route::put('/admin/otorisator/update/{id}', [OtorisatorController::class, 'update'])->name('admin/otorisator/update');
    Route::delete('/admin/otorisator/delete/{id}', [OtorisatorController::class, 'destroy'])->name('admin/otorisator/delete');
});

Route::middleware(['auth', 'role:operator'])->group(function () {
    Route::get('operator/dashboard', [HomeController::class, 'homeOperator'])->name('operator/dashboard');

    // ERP-style modules
    Route::view('operator/master-data', 'modules.master-data.index')->name('operator/master-data');
    Route::view('operator/transactions', 'modules.transactions.index')->name('operator/transactions');
    Route::view('operator/reports', 'modules.reports.index')->name('operator/reports');
    Route::view('operator/finance', 'modules.finance.index')->name('operator/finance');
    Route::view('operator/administration', 'modules.administration.index')->name('operator/administration');
    Route::view('operator/settings', 'modules.settings.index')->name('operator/settings');
    Route::get('operator/master-data/coa-workspace', [COAWorkspaceController::class, 'index'])->name('operator/master-data/coa-workspace');
    Route::post('operator/master-data/coa-workspace/export', [COAWorkspaceController::class, 'exportData'])->name('operator/master-data/coa-workspace.export');
    Route::post('operator/master-data/coa-workspace/import', [COAWorkspaceController::class, 'importStore'])->name('operator/master-data/coa-workspace.import');
    Route::get('operator/master-data/coa-workspace/template', [COAWorkspaceController::class, 'downloadTemplate'])->name('operator/master-data/coa-workspace.template');
    Route::get('operator/transactions/journal-entry', [JournalEntryController::class, 'index'])->name('operator/transactions/journal-entry');
    Route::post('operator/transactions/journal-entry', [JournalEntryController::class, 'store'])->name('operator/transactions/journal-entry.store');

    Route::get('/operator/periodes', [PeriodeController::class, 'index'])->name('operator/periodes');
    Route::get('/operator/periodes/create', [PeriodeController::class, 'create'])->name('operator/periodes/create');
    Route::post('/operator/periodes/save', [PeriodeController::class, 'save'])->name('operator/periodes/save');
    Route::get('/operator/periodes/edit/{id}', [PeriodeController::class, 'update'])->name('operator/periodes/edit');
    Route::put('/operator/periodes/update/{id}', [PeriodeController::class, 'updatesave'])->name('operator/periodes/update');
    Route::get('/operator/periodes/delete/{id}', [PeriodeController::class, 'delete'])->name('operator/periodes/delete');

    Route::get('/operator/account/header', [HeaderController::class, 'index'])->name('operator/account/header');
    Route::get('/operator/account/header/create', [HeaderController::class, 'create'])->name('operator/account/header/create');
    Route::post('/operator/account/header/save', [HeaderController::class, 'save'])->name('operator/account/header/save');
    Route::get('/operator/account/header/edit/{id}', [HeaderController::class, 'update'])->name('operator/account/header/edit');
    Route::put('/operator/account/header/update/{id}', [HeaderController::class, 'updatesave'])->name('operator/account/header/update');
    Route::get('/operator/account/header/delete/{id}', [HeaderController::class, 'delete'])->name('operator/account/header/delete');

    Route::get('/operator/account/coa', [CoaController::class, 'index'])->name('operator/account/coa');
    Route::get('/operator/account/coa/create', [CoaController::class, 'create'])->name('operator/account/coa/create');
    Route::post('/operator/account/coa/save', [CoaController::class, 'save'])->name('operator/account/coa/save');
    Route::get('/operator/account/coa/edit/{id}', [CoaController::class, 'update'])->name('operator/account/coa/edit');
    Route::put('/operator/account/coa/update/{id}', [CoaController::class, 'updatesave'])->name('operator/account/coa/update');
    Route::get('/operator/account/coa/delete/{id}', [CoaController::class, 'delete'])->name('operator/account/coa/delete');


    Route::get('/operator/jurnaling', [JurnalingController::class, 'index'])->name('operator/jurnaling');
    Route::get('/operator/jurnaling/kaskeluar', [JurnalingController::class, 'indexkaskeluar'])->name('operator/jurnaling/kaskeluar');
    Route::get('/operator/jurnaling/bankmasuk', [JurnalingController::class, 'indexbankmasuk'])->name('operator/jurnaling/bankmasuk');
    Route::get('/operator/jurnaling/bankkeluar', [JurnalingController::class, 'indexbankkeluar'])->name('operator/jurnaling/bankkeluar');
    Route::get('/operator/jurnaling/memorial', [JurnalingController::class, 'indexmemorial'])->name('operator/jurnaling/memorial');
    Route::get('/operator/jurnaling/memorialpenutup', [JurnalingController::class, 'indexmemorialpenutup'])->name('operator/jurnaling/memorialpenutup');
    Route::get('/operator/jurnaling/create', [JurnalingController::class, 'create'])->name('operator/jurnaling/create');
    Route::post('/operator/jurnaling/save', [JurnalingController::class, 'save'])->name('operator/jurnaling/save');
    Route::post('/operator/jurnaling/store', [JurnalingController::class, 'store'])->name('operator/jurnaling/store');
    Route::post('/operator/jurnaling/storekaskeluar', [JurnalingController::class, 'storekaskeluar'])->name('operator/jurnaling/storekaskeluar');
    Route::post('/operator/jurnaling/storebankmasuk', [JurnalingController::class, 'storebankmasuk'])->name('operator/jurnaling/storebankmasuk');
    Route::post('/operator/jurnaling/storebankkeluar', [JurnalingController::class, 'storebankkeluar'])->name('operator/jurnaling/storebankkeluar');
    Route::post('/operator/jurnaling/storememorial', [JurnalingController::class, 'storememorial'])->name('operator/jurnaling/storememorial');
    Route::post('/operator/jurnaling/storememorialpenutup', [JurnalingController::class, 'storememorialpenutup'])->name('operator/jurnaling/storememorialpenutup');
    Route::post('operator/jurnaling/unrekap/{periode_id}', [JurnalingController::class, 'unrekapJurnal'])->name('operator/jurnaling/unrekap');
    Route::post('operator/jurnaling/rekap/{periode_id}', [JurnalingController::class, 'rekapJurnal'])->name('operator/jurnaling/rekap');
    Route::get('/operator/jurnaling/showing', [JurnalingController::class, 'showEntries'])->name('operator/jurnaling/showing');
    Route::get('/operator/jurnaling/months', [JurnalingController::class, 'showMonths'])->name('operator/jurnaling/months');
    Route::get('/operator/jurnaling/export', [JurnalingController::class, 'exportJurnaling'])->name('operator/jurnaling/export');

    Route::get('/operator/jurnaling/cek-nomor-buktikm', [JurnalingController::class, 'cekNomorBuktiKM'])->name('operator/cekNomorBuktiKM');
    Route::get('/operator/jurnaling/cek-nomor-buktikk', [JurnalingController::class, 'cekNomorBuktiKK'])->name('operator/cekNomorBuktiKK');
    Route::get('/operator/jurnaling/cek-nomor-buktibm', [JurnalingController::class, 'cekNomorBuktiBM'])->name('operator/cekNomorBuktiBM');
    Route::get('/operator/jurnaling/cek-nomor-buktibk', [JurnalingController::class, 'cekNomorBuktiBK'])->name('operator/cekNomorBuktiBK');
    Route::get('/operator/jurnaling/cek-nomor-buktimem', [JurnalingController::class, 'cekNomorBuktiMem'])->name('operator/cekNomorBuktiMem');
    Route::get('/operator/jurnaling/cek-nomor-buktimempenutup', [JurnalingController::class, 'cekNomorBuktiMemPenutup'])->name('operator/cekNomorBuktiMemPenutup');

    Route::put('/operator/jurnaling/editkm/{id}', [JurnalingController::class, 'updatekm'])->name('operator/jurnaling/updatekm');
    Route::put('/operator/jurnaling/editkk/{id}', [JurnalingController::class, 'updatekk'])->name('operator/jurnaling/updatekk');
    Route::put('/operator/jurnaling/editbm/{id}', [JurnalingController::class, 'updatebm'])->name('operator/jurnaling/updatebm');
    Route::put('/operator/jurnaling/editbk/{id}', [JurnalingController::class, 'updatebk'])->name('operator/jurnaling/updatebk');
    Route::put('/operator/jurnaling/editmem/{id}', [JurnalingController::class, 'updatemem'])->name('operator/jurnaling/updatemem');
    Route::put('/operator/jurnaling/editmempenutup/{id}', [JurnalingController::class, 'updatemempenutup'])->name('operator/jurnaling/updatemempenutup');

    Route::delete('/operator/jurnaling/deletekm/{id}', [JurnalingController::class, 'deletekm'])->name('operator/jurnaling/deletekm');
    Route::delete('/operator/jurnaling/deletekk/{id}', [JurnalingController::class, 'deletekk'])->name('operator/jurnaling/deletekk');
    Route::delete('/operator/jurnaling/deletebk/{id}', [JurnalingController::class, 'deletebk'])->name('operator/jurnaling/deletebk');
    Route::delete('/operator/jurnaling/deletebm/{id}', [JurnalingController::class, 'deletebm'])->name('operator/jurnaling/deletebm');
    Route::delete('/operator/jurnaling/deletemem/{id}', [JurnalingController::class, 'deletemem'])->name('operator/jurnaling/deletemem');
    Route::delete('/operator/jurnaling/deletemempenutup/{id}', [JurnalingController::class, 'deletemempenutup'])->name('operator/jurnaling/deletemempenutup');

    Route::get('/operator/bukubesar', [BukuBesarController::class, 'showLedgerForm'])->name('operator/bukubesar');
    Route::get('/operator/bukubesar/searchCoaByPeriod', [BukuBesarController::class, 'searchCoaByPeriod'])->name('operator/bukubesar/searchCoaByPeriod');
    Route::get('operator/bukubesar/showAll', [BukuBesarController::class, 'showAll'])->name('operator/bukubesar/showAll');
    Route::get('/operator/bukubesar/searchCoaByFilter', [BukuBesarController::class, 'searchCoaByFilter'])->name('operator/bukubesar/searchCoaByFilter');
    Route::get('/operator/bukubesar/searchByDate', [BukuBesarController::class, 'searchByDate'])->name('operator/bukubesar/searchByDate');
    Route::get('/operator/bukubesar/filter', [BukuBesarController::class, 'filterView'])->name('operator/bukubesar/filter');
    Route::get('operator/bukubesar/export', [BukuBesarController::class, 'exportExcel'])->name('operator/bukubesar/export');

    Route::get('/operator/saldoawal', [SaldoAwalController::class, 'index'])->name('operator/saldoawal');
    Route::get('/operator/saldoawal/create', [SaldoAwalController::class, 'create'])->name('operator/saldoawal/create');
    Route::post('/operator/saldoawal/store', [SaldoAwalController::class, 'store'])->name('operator/saldoawal/store');
    Route::get('/operator/saldoawal/{id}/edit', [SaldoAwalController::class, 'edit'])->name('operator.saldoawal.edit');
    Route::put('/operator/saldoawal/{id}', [SaldoAwalController::class, 'update'])->name('operator.saldoawal.update');
    Route::delete('/operator/saldoawal/{id}', [SaldoAwalController::class, 'destroy'])->name('operator.saldoawal.destroy');

    Route::get('/operator/neracasaldo/{periode_id}', [NeracaSaldoController::class, 'index'])->name('operator/neracasaldo');
    Route::get('/operator/neracasaldo/', [NeracaSaldoController::class, 'indexrecap'])->name('operator/neracasaldo/');
    Route::get('/operator/neracasaldo/showing/{periode_id}', [NeracaSaldoController::class, 'indexmon'])->name('operator/neracasaldo/showing');
    Route::get('/operator/neracasaldo/months/{periode?}', [JurnalingController::class, 'showPerMonth'])->name('operator/neracasaldo/months');
    Route::get('/operator/neracasaldo/monthstampil/{periode?}', [NeracaSaldoController::class, 'showPerMonthNeraca'])->name('operator/neracasaldo/monthstampil');
    Route::get('/operator/neracasaldo/rekap/{periode_id}', [JurnalingController::class, 'rekapJurnalMonth'])->name('operator/neracasaldo/rekap');
    Route::get('/operator/neracasaldo/exportexcel/{periode_id}', [NeracaSaldoController::class, 'exportExcel'])->name('operator/neracasaldo/exportexcel');
    Route::get('/operator/neracasaldo/exportpdf/{periode_id}', [NeracaSaldoController::class, 'exportPdf'])->name('operator/neracasaldo/exportpdf');

    Route::get('/operator/otorisator/home', [OtorisatorController::class, 'index'])->name('operator/otorisator/home');
    Route::get('/operator/otorisator/create', [OtorisatorController::class, 'create'])->name('operator/otorisator/create');
    Route::post('/operator/otorisator/save', [OtorisatorController::class, 'store'])->name('operator/otorisator/save');
    Route::get('/operator/otorisator/edit/{id}', [OtorisatorController::class, 'edit'])->name('operator/otorisator/edit');
    Route::put('/operator/otorisator/update/{id}', [OtorisatorController::class, 'update'])->name('operator/otorisator/update');
    Route::delete('/operator/otorisator/delete/{id}', [OtorisatorController::class, 'destroy'])->name('operator/otorisator/delete');
});

Route::middleware(['auth', 'role:bod'])->group(function () {
    Route::get('bod/dashboard', [HomeController::class, 'homeBod'])->name('bod/dashboard');

    // ERP-style modules
    Route::view('bod/master-data', 'modules.master-data.index')->name('bod/master-data');
    Route::view('bod/transactions', 'modules.transactions.index')->name('bod/transactions');
    Route::view('bod/reports', 'modules.reports.index')->name('bod/reports');
    Route::view('bod/finance', 'modules.finance.index')->name('bod/finance');
    Route::view('bod/administration', 'modules.administration.index')->name('bod/administration');
    Route::view('bod/settings', 'modules.settings.index')->name('bod/settings');
    Route::get('bod/master-data/coa-workspace', [COAWorkspaceController::class, 'index'])->name('bod/master-data/coa-workspace');
    Route::post('bod/master-data/coa-workspace/export', [COAWorkspaceController::class, 'exportData'])->name('bod/master-data/coa-workspace.export');
    Route::post('bod/master-data/coa-workspace/import', [COAWorkspaceController::class, 'importStore'])->name('bod/master-data/coa-workspace.import');
    Route::get('bod/master-data/coa-workspace/template', [COAWorkspaceController::class, 'downloadTemplate'])->name('bod/master-data/coa-workspace.template');
    Route::get('bod/transactions/journal-entry', [JournalEntryController::class, 'index'])->name('bod/transactions/journal-entry');
    Route::post('bod/transactions/journal-entry', [JournalEntryController::class, 'store'])->name('bod/transactions/journal-entry.store');

    Route::get('/bod/jurnaling/showing', [JurnalingController::class, 'showEntries'])->name('bod/jurnaling/showing');
    Route::get('/bod/jurnaling/months', [JurnalingController::class, 'showMonths'])->name('bod/jurnaling/months');
    Route::get('/bod/jurnaling/export', [JurnalingController::class, 'exportJurnaling'])->name('bod/jurnaling/export');

    Route::get('/bod/bukubesar', [BukuBesarController::class, 'showLedgerForm'])->name('bod/bukubesar');
    Route::get('/bod/bukubesar/searchCoaByPeriod', [BukuBesarController::class, 'searchCoaByPeriod'])->name('bod/bukubesar/searchCoaByPeriod');
    Route::get('bod/bukubesar/showAll', [BukuBesarController::class, 'showAll'])->name('bod/bukubesar/showAll');
    Route::get('/bod/bukubesar/searchCoaByFilter', [BukuBesarController::class, 'searchCoaByFilter'])->name('bod/bukubesar/searchCoaByFilter');
    Route::get('/bod/bukubesar/searchByDate', [BukuBesarController::class, 'searchByDate'])->name('bod/bukubesar/searchByDate');
    Route::get('/bod/bukubesar/filter', [BukuBesarController::class, 'filterView'])->name('bod/bukubesar/filter');
    Route::get('bod/bukubesar/export', [BukuBesarController::class, 'exportExcel'])->name('bod/bukubesar/export');

    Route::get('/bod/neracasaldo/{periode_id}', [NeracaSaldoController::class, 'index'])->name('bod/neracasaldo');
    Route::get('/bod/neracasaldo/', [NeracaSaldoController::class, 'indexrecap'])->name('bod/neracasaldo/');
    Route::get('/bod/neracasaldo/showing/{periode_id}', [NeracaSaldoController::class, 'indexmon'])->name('bod/neracasaldo/showing');
    Route::get('/bod/neracasaldo/months/{periode?}', [JurnalingController::class, 'showPerMonth'])->name('bod/neracasaldo/months');
    Route::get('/bod/neracasaldo/monthstampil/{periode?}', [NeracaSaldoController::class, 'showPerMonthNeraca'])->name('bod/neracasaldo/monthstampil');
    Route::get('/bod/neracasaldo/rekap/{periode_id}', [JurnalingController::class, 'rekapJurnalMonth'])->name('bod/neracasaldo/rekap');
    Route::get('/bod/neracasaldo/exportexcel/{periode_id}', [NeracaSaldoController::class, 'exportExcel'])->name('bod/neracasaldo/exportexcel');
    Route::get('/bod/neracasaldo/exportpdf/{periode_id}', [NeracaSaldoController::class, 'exportPdf'])->name('bod/neracasaldo/exportpdf');
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toIso8601String(),
        'app' => config('app.name'),
        'env' => config('app.env'),
    ]);
});

require __DIR__ . '/auth.php';
