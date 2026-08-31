<?php

use App\Http\Controllers\ActivityController;
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
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
| Demo Mode
|--------------------------------------------------------------------------
*/
Route::get('/demo-login', function () {
    if (! app()->isLocal() && ! config('app.demo_enabled', false)) {
        abort(404);
    }

    $demoEmail = 'demo@dapense.app';

    try {
        $user = User::where('email', $demoEmail)->first();

        if (! $user) {
            $user = User::create([
                'name' => 'Demo User',
                'email' => $demoEmail,
                'password' => Hash::make('demo-password'),
                'usertype' => 'rootsuperuser',
                'status' => 1,
                'email_verified_at' => now(),
            ]);
        }

        // Demo mode: bypass email verification so the demo user can access all menus.
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        Auth::login($user);

        return redirect()->intended('rootsuperuser/dashboard');
    } catch (QueryException|PDOException $e) {
        report($e);

        $hint = config('database.connections.mysql.host') === 'dapense-mysql' && ! config('app.docker')
            ? 'DB host dapense-mysql is only reachable inside Docker. Set DB_HOST=127.0.0.1 and DB_PORT=13306 (docker MySQL) or run `docker compose up`.'
            : 'Database unavailable. Check DB_HOST/DB_PORT and that MySQL is running (`docker compose ps`).';

        return response()->view('errors.db-unavailable', [
            'hint' => $hint,
            'message' => $e->getMessage(),
        ], 503);
    }
})->middleware('throttle:6,1')->name('demo.login');

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
    Route::get('/activity', [ActivityController::class, 'index'])->name('activity');
    Route::view('/master-data', 'modules.master-data.index')->name('master-data');
    Route::get('/coa-workspace', COAWorkspace::class)->name('coa-workspace');
    Route::post('/coa-workspace/export', [COAWorkspaceController::class, 'exportData'])->name('coa-workspace.export')->middleware('throttle:export');
    Route::post('/coa-workspace/import', [COAWorkspaceController::class, 'importStore'])->name('coa-workspace.import')->middleware('throttle:import');
    Route::get('/coa-workspace/template', [COAWorkspaceController::class, 'downloadTemplate'])->name('coa-workspace.template');
    Route::view('/transactions', 'modules.transactions.index')->name('transactions');
    Route::get('/jurnal-entry', JournalEntry::class)->name('jurnal-entry');
    Route::post('/jurnal-entry', [JournalEntryController::class, 'store'])->name('jurnal-entry.store');
    Route::get('/jurnaling', JurnalManager::class)->name('jurnaling');
    Route::get('/jurnaling-list', JurnalList::class)->name('jurnaling-list');
    Route::get('/jurnaling/export', [JurnalingController::class, 'exportJurnaling'])->name('jurnaling.export')->middleware('throttle:export');
    Route::view('/reports', 'modules.reports.index')->name('reports');
    Route::get('/bukubesar', BukuBesar::class)->name('bukubesar');
    Route::get('/bukubesar/export', [BukuBesarController::class, 'exportExcel'])->name('bukubesar.export')->middleware('throttle:export');
    Route::get('/neraca-saldo/{periode?}', NeracaSaldo::class)->name('neraca-saldo');
    Route::get('/neraca-saldo/exportexcel/{periode_id}', [NeracaSaldoController::class, 'exportExcel'])->name('neraca-saldo.exportexcel')->middleware('throttle:export');
    Route::get('/neraca-saldo/exportpdf/{periode_id}', [NeracaSaldoController::class, 'exportPdf'])->name('neraca-saldo.exportpdf')->middleware('throttle:export');
    Route::view('/finance', 'modules.finance.index')->name('finance');
    Route::get('/saldo-awal', SaldoAwal::class)->name('saldo-awal');
    Route::get('/periodes', PeriodeManager::class)->name('periodes');
    Route::get('/otorisator', OtorisatorManager::class)->name('otorisator');
    Route::view('/administration', 'modules.administration.index')->name('administration');
    Route::get('/users', UserManager::class)->name('users');
    Route::get('/posting', Posting::class)->name('posting');
    Route::post('/posting', [PostingControllerRootSuperuser::class, 'postJurnal'])->name('posting.post')->middleware('throttle:posting');
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
    Route::post('/master-data/coa-workspace/export', [COAWorkspaceController::class, 'exportData'])->name('master-data/coa-workspace.export')->middleware('throttle:export');
    Route::post('/master-data/coa-workspace/import', [COAWorkspaceController::class, 'importStore'])->name('master-data/coa-workspace.import')->middleware('throttle:import');
    Route::get('/master-data/coa-workspace/template', [COAWorkspaceController::class, 'downloadTemplate'])->name('master-data/coa-workspace.template');
    Route::get('/transactions/journal-entry', [JournalEntryController::class, 'index'])->name('transactions/journal-entry');
    Route::post('/transactions/journal-entry', [JournalEntryController::class, 'store'])->name('transactions/journal-entry.store');

    Route::get('/account/header', [HeaderController::class, 'index'])->name('account/header');
    Route::get('/account/header/create', [HeaderController::class, 'create'])->name('account/header/create');
    Route::post('/account/header/save', [HeaderController::class, 'save'])->name('account/header/save');
    Route::get('/account/header/edit/{id}', [HeaderController::class, 'update'])->name('account/header/edit');
    Route::put('/account/header/update/{id}', [HeaderController::class, 'updateSave'])->name('account/header/update');
    Route::get('/account/header/delete/{id}', [HeaderController::class, 'delete'])->name('account/header/delete');

    Route::get('/account/coa', [CoaController::class, 'index'])->name('account/coa');
    Route::get('/account/coa/create', [CoaController::class, 'create'])->name('account/coa/create');
    Route::post('/account/coa/save', [CoaController::class, 'save'])->name('account/coa/save');
    Route::get('/account/coa/edit/{id}', [CoaController::class, 'update'])->name('account/coa/edit');
    Route::put('/account/coa/update/{id}', [CoaController::class, 'updateSave'])->name('account/coa/update');
    Route::get('/account/coa/delete/{id}', [CoaController::class, 'delete'])->name('account/coa/delete');

    Route::get('/periodes', [PeriodeController::class, 'index'])->name('periodes');
    Route::get('/periodes/create', [PeriodeController::class, 'create'])->name('periodes/create');
    Route::post('/periodes/save', [PeriodeController::class, 'save'])->name('periodes/save');
    Route::get('/periodes/edit/{id}', [PeriodeController::class, 'update'])->name('periodes/edit');
    Route::put('/periodes/update/{id}', [PeriodeController::class, 'updateSave'])->name('periodes/update');
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
    Route::get('/jurnaling/export', [JurnalingController::class, 'exportJurnaling'])->name('jurnaling/export')->middleware('throttle:export');
    Route::get('/jurnaling/cek-nomor-buktikm', [JurnalingController::class, 'cekNomorBuktiKM'])->name('cekNomorBuktiKM');
    Route::put('/jurnaling/editkm/{id}', [JurnalingController::class, 'updatekm'])->name('jurnaling/updatekm');

    Route::get('/bukubesar', [BukuBesarController::class, 'showLedgerForm'])->name('bukubesar');
    Route::get('/bukubesar/searchCoaByPeriod', [BukuBesarController::class, 'searchCoaByPeriod'])->name('bukubesar/searchCoaByPeriod');
    Route::get('/bukubesar/showAll', [BukuBesarController::class, 'showAll'])->name('bukubesar/showAll');
    Route::get('/bukubesar/export', [BukuBesarController::class, 'exportExcel'])->name('bukubesar/export');

    Route::get('/saldoawal', [SaldoAwalController::class, 'index'])->name('saldoawal');
    Route::get('/saldoawal/create', [SaldoAwalController::class, 'create'])->name('saldoawal/create');
    Route::post('/saldoawal/store', [SaldoAwalController::class, 'store'])->name('saldoawal/store');
    Route::get('/saldoawal/{id}/edit', [SaldoAwalController::class, 'edit'])->name('saldoawal/edit');
    Route::put('/saldoawal/{id}/update', [SaldoAwalController::class, 'update'])->name('saldoawal/update');
    Route::delete('/saldoawal/{id}/destroy', [SaldoAwalController::class, 'destroy'])->name('saldoawal/destroy');

    Route::get('/posting', [PostingControllerRootSuperuser::class, 'index'])->name('posting');
    Route::post('/posting', [PostingControllerRootSuperuser::class, 'postJurnal'])->name('posting/post')->middleware('throttle:posting');

    Route::get('/neracasaldo/{periode_id}', [NeracaSaldoController::class, 'index'])->name('neracasaldo');
    Route::get('/neracasaldo/', [NeracaSaldoController::class, 'indexrecap'])->name('neracasaldo/');
    Route::get('/neracasaldo/showing/{periode_id}', [NeracaSaldoController::class, 'indexmon'])->name('neracasaldo/showing');
    Route::get('/neracasaldo/months/{periode?}', [JurnalingController::class, 'showPerMonth'])->name('neracasaldo/months');
    Route::get('/neracasaldo/monthstampil/{periode?}', [NeracaSaldoController::class, 'showPerMonthNeraca'])->name('neracasaldo/monthstampil');
    Route::get('/neracasaldo/rekap/{periode_id}', [JurnalingController::class, 'rekapJurnalMonth'])->name('neracasaldo/rekap');
    Route::get('/neracasaldo/exportexcel/{periode_id}', [NeracaSaldoController::class, 'exportExcel'])->name('neracasaldo/exportexcel')->middleware('throttle:export');
    Route::get('/neracasaldo/exportpdf/{periode_id}', [NeracaSaldoController::class, 'exportPdf'])->name('neracasaldo/exportpdf')->middleware('throttle:export');

    Route::get('/otorisator/home', [OtorisatorController::class, 'index'])->name('otorisator/home');
    Route::get('/otorisator/create', [OtorisatorController::class, 'create'])->name('otorisator/create');
    Route::post('/otorisator/save', [OtorisatorController::class, 'store'])->name('otorisator/save');
    Route::get('/otorisator/edit/{id}', [OtorisatorController::class, 'edit'])->name('otorisator/edit');
    Route::put('/otorisator/update/{id}', [OtorisatorController::class, 'update'])->name('otorisator/update');
    Route::delete('/otorisator/delete/{id}', [OtorisatorController::class, 'destroy'])->name('otorisator/delete');
});

/* ===================================================================
   CONSOLIDATED LEGACY ROUTES — single set of controller routes
   protected by Policy classes for authorization.
   =================================================================== */
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', [HomeController::class, 'index'])->name('admin/dashboard')->middleware('role:admin');
    Route::get('/operator/dashboard', [HomeController::class, 'homeOperator'])->name('operator/dashboard')->middleware('role:operator');
    Route::get('/bod/dashboard', [HomeController::class, 'homeBod'])->name('bod/dashboard')->middleware('role:bod');
});

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
        'app' => config('app.name'),
        'env' => config('app.env'),
    ]);
});

require __DIR__ . '/auth.php';
