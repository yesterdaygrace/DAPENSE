<?php

use App\Http\Controllers\admin\ProductControllerAdmin;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Base\BukuBesarController;
use App\Http\Controllers\Base\CoaController;
use App\Http\Controllers\Base\HeaderCoaController;
use App\Http\Controllers\Base\HeaderController;
use App\Http\Controllers\Base\JurnalingController;
use App\Http\Controllers\Base\NeracaSaldoController;
use App\Http\Controllers\Base\OtorisatorController;
use App\Http\Controllers\Base\PeriodeController;
use App\Http\Controllers\Base\SaldoAwalController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\rootsuperuser\PostingControllerRootSuperuser;
use App\Http\Controllers\rootsuperuser\ProductControllerRootSuperuser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view(Auth::user()->usertype.'.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/logout', [AuthenticatedSessionController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'role:rootsuperuser'])->group(function () {
    Route::get('rootsuperuser/dashboard', [HomeController::class, 'homerootsuperuser'])->name('rootsuperuser/dashboard');
    Route::get('/rootsuperuser/products', [ProductControllerRootSuperuser::class, 'index'])->name('rootsuperuser/products');
    Route::get('/rootsuperuser/products/create', [ProductControllerRootSuperuser::class, 'create'])->name('rootsuperuser/products/create');
    Route::post('/rootsuperuser/products/save', [ProductControllerRootSuperuser::class, 'save'])->name('rootsuperuser/products/save');
    Route::get('/rootsuperuser/products/edit/{id}', [ProductControllerRootSuperuser::class, 'edit'])->name('rootsuperuser/products/edit');
    Route::put('/rootsuperuser/products/update/{id}', [ProductControllerRootSuperuser::class, 'update'])->name('rootsuperuser/products/update');
    Route::get('/rootsuperuser/products/delete/{id}', [ProductControllerRootSuperuser::class, 'delete'])->name('rootsuperuser/products/delete');
    Route::get('/rootsuperuser/products/status/{id}', [ProductControllerRootSuperuser::class, 'toggleStatus'])->name('rootsuperuser/products/status');

    Route::get('/rootsuperuser/account/header', [HeaderController::class, 'index'])->name('rootsuperuser/account/header');
    Route::get('/rootsuperuser/account/header/create', [HeaderController::class, 'create'])->name('rootsuperuser/account/header/create');
    Route::post('/rootsuperuser/account/header/save', [HeaderController::class, 'save'])->name('rootsuperuser/account/header/save');
    Route::get('/rootsuperuser/account/header/edit/{id}', [HeaderController::class, 'update'])->name('rootsuperuser/account/header/edit');
    Route::put('/rootsuperuser/account/header/update/{id}', [HeaderController::class, 'updatesave'])->name('rootsuperuser/account/header/update');
    Route::get('/rootsuperuser/account/header/delete/{id}', [HeaderController::class, 'delete'])->name('rootsuperuser/account/header/delete');

    Route::get('/rootsuperuser/account/coa', [CoaController::class, 'index'])->name('rootsuperuser/account/coa');
    Route::get('/rootsuperuser/account/coa/create', [CoaController::class, 'create'])->name('rootsuperuser/account/coa/create');
    Route::post('/rootsuperuser/account/coa/save', [CoaController::class, 'save'])->name('rootsuperuser/account/coa/save');
    Route::get('/rootsuperuser/account/coa/edit/{id}', [CoaController::class, 'update'])->name('rootsuperuser/account/coa/edit');
    Route::put('/rootsuperuser/account/coa/update/{id}', [CoaController::class, 'updatesave'])->name('rootsuperuser/account/coa/update');
    Route::get('/rootsuperuser/account/coa/delete/{id}', [CoaController::class, 'delete'])->name('rootsuperuser/account/coa/delete');

    Route::get('rootsuperuser/account/headercoa', [HeaderCoaController::class, 'index'])->name('rootsuperuser/account/headercoa');

    Route::get('/rootsuperuser/periodes', [PeriodeController::class, 'index'])->name('rootsuperuser/periodes');
    Route::get('/rootsuperuser/periodes/create', [PeriodeController::class, 'create'])->name('rootsuperuser/periodes/create');
    Route::post('/rootsuperuser/periodes/save', [PeriodeController::class, 'save'])->name('rootsuperuser/periodes/save');
    Route::get('/rootsuperuser/periodes/edit/{id}', [PeriodeController::class, 'update'])->name('rootsuperuser/periodes/edit');
    Route::put('/rootsuperuser/periodes/update/{id}', [PeriodeController::class, 'updatesave'])->name('rootsuperuser/periodes/update');
    Route::get('/rootsuperuser/periodes/delete/{id}', [PeriodeController::class, 'delete'])->name('rootsuperuser/periodes/delete');

    Route::get('/rootsuperuser/jurnaling', [JurnalingController::class, 'index'])->name('rootsuperuser/jurnaling');
    Route::get('/rootsuperuser/jurnaling/kaskeluar', [JurnalingController::class, 'indexkaskeluar'])->name('rootsuperuser/jurnaling/kaskeluar');
    Route::get('/rootsuperuser/jurnaling/bankmasuk', [JurnalingController::class, 'indexbankmasuk'])->name('rootsuperuser/jurnaling/bankmasuk');
    Route::get('/rootsuperuser/jurnaling/bankkeluar', [JurnalingController::class, 'indexbankkeluar'])->name('rootsuperuser/jurnaling/bankkeluar');
    Route::get('/rootsuperuser/jurnaling/memorial', [JurnalingController::class, 'indexmemorial'])->name('rootsuperuser/jurnaling/memorial');
    Route::get('/rootsuperuser/jurnaling/memorialpenutup', [JurnalingController::class, 'indexmemorialpenutup'])->name('rootsuperuser/jurnaling/memorialpenutup');
    Route::get('/rootsuperuser/jurnaling/create', [JurnalingController::class, 'create'])->name('rootsuperuser/jurnaling/create');
    Route::post('/rootsuperuser/jurnaling/store', [JurnalingController::class, 'store'])->name('rootsuperuser/jurnaling/store');
    Route::post('/rootsuperuser/jurnaling/storekaskeluar', [JurnalingController::class, 'storekaskeluar'])->name('rootsuperuser/jurnaling/storekaskeluar');
    Route::post('/rootsuperuser/jurnaling/storebankmasuk', [JurnalingController::class, 'storebankmasuk'])->name('rootsuperuser/jurnaling/storebankmasuk');
    Route::post('/rootsuperuser/jurnaling/storebankkeluar', [JurnalingController::class, 'storebankkeluar'])->name('rootsuperuser/jurnaling/storebankkeluar');
    Route::post('/rootsuperuser/jurnaling/storememorial', [JurnalingController::class, 'storememorial'])->name('rootsuperuser/jurnaling/storememorial');
    Route::post('/rootsuperuser/jurnaling/storememorialpenutup', [JurnalingController::class, 'storememorialpenutup'])->name('rootsuperuser/jurnaling/storememorialpenutup');
    Route::post('rootsuperuser/jurnaling/unrekap/{periode_id}', [JurnalingController::class, 'unrekapJurnal'])->name('rootsuperuser/jurnaling/unrekap');
    Route::post('rootsuperuser/jurnaling/rekap/{periode_id}', [JurnalingController::class, 'rekapJurnal'])->name('rootsuperuser/jurnaling/rekap');
    Route::get('/rootsuperuser/jurnaling/showing', [JurnalingController::class, 'showEntries'])->name('rootsuperuser/jurnaling/showing');
    Route::get('/rootsuperuser/jurnaling/months', [JurnalingController::class, 'showMonths'])->name('rootsuperuser/jurnaling/months');
    Route::get('/rootsuperuser/jurnaling/export', [JurnalingController::class, 'exportJurnaling'])->name('rootsuperuser/jurnaling/export');

    Route::get('/rootsuperuser/jurnaling/cek-nomor-buktikm', [JurnalingController::class, 'cekNomorBuktiKM'])->name('cekNomorBuktiKM');
    Route::get('/rootsuperuser/jurnaling/cek-nomor-buktikk', [JurnalingController::class, 'cekNomorBuktiKK'])->name('cekNomorBuktiKK');
    Route::get('/rootsuperuser/jurnaling/cek-nomor-buktibm', [JurnalingController::class, 'cekNomorBuktiBM'])->name('cekNomorBuktiBM');
    Route::get('/rootsuperuser/jurnaling/cek-nomor-buktibk', [JurnalingController::class, 'cekNomorBuktiBK'])->name('cekNomorBuktiBK');
    Route::get('/rootsuperuser/jurnaling/cek-nomor-buktimem', [JurnalingController::class, 'cekNomorBuktiMem'])->name('cekNomorBuktiMem');
    Route::get('/rootsuperuser/jurnaling/cek-nomor-buktimempenutup', [JurnalingController::class, 'cekNomorBuktiMemPenutup'])->name('cekNomorBuktiMemPenutup');
    Route::put('/rootsuperuser/jurnaling/editkm/{id}', [JurnalingController::class, 'updatekm'])->name('rootsuperuser/jurnaling/updatekm');
    Route::put('/rootsuperuser/jurnaling/editkk/{id}', [JurnalingController::class, 'updatekk'])->name('rootsuperuser/jurnaling/updatekk');
    Route::put('/rootsuperuser/jurnaling/editbm/{id}', [JurnalingController::class, 'updatebm'])->name('rootsuperuser/jurnaling/updatebm');
    Route::put('/rootsuperuser/jurnaling/editbk/{id}', [JurnalingController::class, 'updatebk'])->name('rootsuperuser/jurnaling/updatebk');
    Route::put('/rootsuperuser/jurnaling/editmem/{id}', [JurnalingController::class, 'updatemem'])->name('rootsuperuser/jurnaling/updatemem');
    Route::put('/rootsuperuser/jurnaling/editmempenutup/{id}', [JurnalingController::class, 'updatemempenutup'])->name('rootsuperuser/jurnaling/updatemempenutup');

    Route::delete('/rootsuperuser/jurnaling/deletekm/{id}', [JurnalingController::class, 'deletekm'])->name('rootsuperuser/jurnaling/deletekm');
    Route::delete('/rootsuperuser/jurnaling/deletekk/{id}', [JurnalingController::class, 'deletekk'])->name('rootsuperuser/jurnaling/deletekk');
    Route::delete('/rootsuperuser/jurnaling/deletebk/{id}', [JurnalingController::class, 'deletebk'])->name('rootsuperuser/jurnaling/deletebk');
    Route::delete('/rootsuperuser/jurnaling/deletebm/{id}', [JurnalingController::class, 'deletebm'])->name('rootsuperuser/jurnaling/deletebm');
    Route::delete('/rootsuperuser/jurnaling/deletemem/{id}', [JurnalingController::class, 'deletemem'])->name('rootsuperuser/jurnaling/deletemem');
    Route::delete('/rootsuperuser/jurnaling/deletemempenutup/{id}', [JurnalingController::class, 'deletemempenutup'])->name('rootsuperuser/jurnaling/deletemempenutup');

    Route::get('/rootsuperuser/bukubesar', [BukuBesarController::class, 'showLedgerForm'])->name('rootsuperuser/bukubesar');
    Route::get('/rootsuperuser/bukubesar/searchCoaByPeriod', [BukuBesarController::class, 'searchCoaByPeriod'])->name('rootsuperuser/bukubesar/searchCoaByPeriod');
    Route::get('rootsuperuser/bukubesar/showAll', [BukuBesarController::class, 'showAll'])->name('rootsuperuser/bukubesar/showAll');
    Route::get('/rootsuperuser/bukubesar/searchCoaByFilter', [BukuBesarController::class, 'searchCoaByFilter'])->name('rootsuperuser/bukubesar/searchCoaByFilter');
    Route::get('/rootsuperuser/bukubesar/searchByDate', [BukuBesarController::class, 'searchByDate'])->name('rootsuperuser/bukubesar/searchByDate');
    Route::get('/rootsuperuser/bukubesar/filter', [BukuBesarController::class, 'filterView'])->name('rootsuperuser/bukubesar/filter');
    Route::get('rootsuperuser/bukubesar/export', [BukuBesarController::class, 'exportExcel'])->name('rootsuperuser/bukubesar/export');

    Route::get('/rootsuperuser/saldoawal', [SaldoAwalController::class, 'index'])->name('rootsuperuser/saldoawal');
    Route::get('/rootsuperuser/saldoawal/create', [SaldoAwalController::class, 'create'])->name('rootsuperuser/saldoawal/create');
    Route::post('/rootsuperuser/saldoawal/store', [SaldoAwalController::class, 'store'])->name('rootsuperuser/saldoawal/store');
    Route::get('/rootsuperuser/saldoawal/{id}/edit', [SaldoAwalController::class, 'edit'])->name('rootsuperuser.saldoawal.edit');
    Route::put('/rootsuperuser/saldoawal/{id}', [SaldoAwalController::class, 'update'])->name('rootsuperuser.saldoawal.update');
    Route::delete('/rootsuperuser/saldoawal/{id}', [SaldoAwalController::class, 'destroy'])->name('rootsuperuser.saldoawal.destroy');

    Route::get('rootsuperuser/posting', [PostingControllerRootSuperuser::class, 'index'])->name('rootsuperuser/posting');
    Route::post('/rootsuperuser/posting', [PostingControllerRootSuperuser::class, 'postJurnal'])->name('rootsuperuser/posting/post');

    Route::get('/rootsuperuser/neracasaldo/{periode_id}', [NeracaSaldoController::class, 'index'])->name('rootsuperuser/neracasaldo');
    Route::get('/rootsuperuser/neracasaldo/', [NeracaSaldoController::class, 'indexrecap'])->name('rootsuperuser/neracasaldo/');
    Route::get('/rootsuperuser/neracasaldo/showing/{periode_id}', [NeracaSaldoController::class, 'indexmon'])->name('rootsuperuser/neracasaldo/showing');
    Route::get('/rootsuperuser/neracasaldo/months/{periode?}', [JurnalingController::class, 'showPerMonth'])->name('rootsuperuser/neracasaldo/months');
    Route::get('/rootsuperuser/neracasaldo/monthstampil/{periode?}', [NeracaSaldoController::class, 'showPerMonthNeraca'])->name('rootsuperuser/neracasaldo/monthstampil');
    Route::get('/rootsuperuser/neracasaldo/rekap/{periode_id}', [JurnalingController::class, 'rekapJurnalMonth'])->name('rootsuperuser/neracasaldo/rekap');
    Route::get('/rootsuperuser/neracasaldo/exportexcel/{periode_id}', [NeracaSaldoController::class, 'exportExcel'])->name('rootsuperuser/neracasaldo/exportexcel');
    Route::get('/rootsuperuser/neracasaldo/exportpdf/{periode_id}', [NeracaSaldoController::class, 'exportPdf'])->name('rootsuperuser/neracasaldo/exportpdf');

    Route::get('/rootsuperuser/otorisator/home', [OtorisatorController::class, 'index'])->name('rootsuperuser/otorisator/home');
    Route::get('/rootsuperuser/otorisator/create', [OtorisatorController::class, 'create'])->name('rootsuperuser/otorisator/create');
    Route::post('/rootsuperuser/otorisator/save', [OtorisatorController::class, 'store'])->name('rootsuperuser/otorisator/save');
    Route::get('/rootsuperuser/otorisator/edit/{id}', [OtorisatorController::class, 'edit'])->name('rootsuperuser/otorisator/edit');
    Route::put('/rootsuperuser/otorisator/update/{id}', [OtorisatorController::class, 'update'])->name('rootsuperuser/otorisator/update');
    Route::delete('/rootsuperuser/otorisator/delete/{id}', [OtorisatorController::class, 'destroy'])->name('rootsuperuser/otorisator/delete');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('admin/dashboard', [HomeController::class, 'index'])->name('admin/dashboard');
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

    Route::get('admin/account/headercoa', [HeaderCoaController::class, 'index'])->name('admin/account/headercoa');

    Route::get('/admin/jurnaling', [JurnalingController::class, 'index'])->name('admin/jurnaling');
    Route::get('/admin/jurnaling/kaskeluar', [JurnalingController::class, 'indexkaskeluar'])->name('admin/jurnaling/kaskeluar');
    Route::get('/admin/jurnaling/bankmasuk', [JurnalingController::class, 'indexbankmasuk'])->name('admin/jurnaling/bankmasuk');
    Route::get('/admin/jurnaling/bankkeluar', [JurnalingController::class, 'indexbankkeluar'])->name('admin/jurnaling/bankkeluar');
    Route::get('/admin/jurnaling/memorial', [JurnalingController::class, 'indexmemorial'])->name('admin/jurnaling/memorial');
    Route::get('/admin/jurnaling/memorialpenutup', [JurnalingController::class, 'indexmemorialpenutup'])->name('admin/jurnaling/memorialpenutup');
    Route::get('/admin/jurnaling/create', [JurnalingController::class, 'create'])->name('admin/jurnaling/create');
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

    Route::get('/admin/jurnaling/cek-nomor-buktikm', [JurnalingController::class, 'cekNomorBuktiKM'])->name('cekNomorBuktiKM');
    Route::get('/admin/jurnaling/cek-nomor-buktikk', [JurnalingController::class, 'cekNomorBuktiKK'])->name('cekNomorBuktiKK');
    Route::get('/admin/jurnaling/cek-nomor-buktibm', [JurnalingController::class, 'cekNomorBuktiBM'])->name('cekNomorBuktiBM');
    Route::get('/admin/jurnaling/cek-nomor-buktibk', [JurnalingController::class, 'cekNomorBuktiBK'])->name('cekNomorBuktiBK');
    Route::get('/admin/jurnaling/cek-nomor-buktimem', [JurnalingController::class, 'cekNomorBuktiMem'])->name('cekNomorBuktiMem');
    Route::get('/admin/jurnaling/cek-nomor-buktimempenutup', [JurnalingController::class, 'cekNomorBuktiMemPenutup'])->name('cekNomorBuktiMemPenutup');
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

    Route::get('operator/account/headercoa', [HeaderCoaController::class, 'index'])->name('operator/account/headercoa');

    Route::get('/operator/jurnaling', [JurnalingController::class, 'index'])->name('operator/jurnaling');
    Route::get('/operator/jurnaling/kaskeluar', [JurnalingController::class, 'indexkaskeluar'])->name('operator/jurnaling/kaskeluar');
    Route::get('/operator/jurnaling/bankmasuk', [JurnalingController::class, 'indexbankmasuk'])->name('operator/jurnaling/bankmasuk');
    Route::get('/operator/jurnaling/bankkeluar', [JurnalingController::class, 'indexbankkeluar'])->name('operator/jurnaling/bankkeluar');
    Route::get('/operator/jurnaling/memorial', [JurnalingController::class, 'indexmemorial'])->name('operator/jurnaling/memorial');
    Route::get('/operator/jurnaling/memorialpenutup', [JurnalingController::class, 'indexmemorialpenutup'])->name('operator/jurnaling/memorialpenutup');
    Route::get('/operator/jurnaling/create', [JurnalingController::class, 'create'])->name('operator/jurnaling/create');
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

    Route::get('/operator/jurnaling/cek-nomor-buktikm', [JurnalingController::class, 'cekNomorBuktiKM'])->name('cekNomorBuktiKM');
    Route::get('/operator/jurnaling/cek-nomor-buktikk', [JurnalingController::class, 'cekNomorBuktiKK'])->name('cekNomorBuktiKK');
    Route::get('/operator/jurnaling/cek-nomor-buktibm', [JurnalingController::class, 'cekNomorBuktiBM'])->name('cekNomorBuktiBM');
    Route::get('/operator/jurnaling/cek-nomor-buktibk', [JurnalingController::class, 'cekNomorBuktiBK'])->name('cekNomorBuktiBK');
    Route::get('/operator/jurnaling/cek-nomor-buktimem', [JurnalingController::class, 'cekNomorBuktiMem'])->name('cekNomorBuktiMem');
    Route::get('/operator/jurnaling/cek-nomor-buktimempenutup', [JurnalingController::class, 'cekNomorBuktiMemPenutup'])->name('cekNomorBuktiMemPenutup');

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

require __DIR__.'/auth.php';
