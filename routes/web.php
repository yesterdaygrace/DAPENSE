<?php

use App\Http\Controllers\admin\BukuBesarControllerAdmin;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PeriodeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\rootsuperuser\CoaControllerRootSuperuser;
use App\Http\Controllers\rootsuperuser\HeaderCoaControllerRootSuperuser;
use App\Http\Controllers\rootsuperuser\HeaderControllerRootSuperuser;
use App\Http\Controllers\rootsuperuser\JurnalingControllerRootSuperuser;
use App\Http\Controllers\rootsuperuser\NeracaSaldoControllerRootSuperuser;
use App\Http\Controllers\rootsuperuser\PeriodeControllerRootSuperuser;
use App\Http\Controllers\rootsuperuser\ProductControllerRootSuperuser;
use App\Http\Controllers\rootsuperuser\BukuBesarControllerRootSuperuser;
use App\Http\Controllers\rootsuperuser\PostingControlerRootSuperuser;
use App\Http\Controllers\rootsuperuser\PostingController;
use App\Http\Controllers\rootsuperuser\SaldoAwalControllerRootSuperuser;
use App\Http\Controllers\admin\ProductControllerAdmin;
use App\Http\Controllers\admin\PeriodeControllerAdmin;
use App\Http\Controllers\admin\HeaderCoaControllerAdmin;
use App\Http\Controllers\admin\CoaControllerAdmin;
use App\Http\Controllers\admin\HeaderControllerAdmin;
use App\Http\Controllers\admin\JurnalingControllerAdmin;
use App\Http\Controllers\admin\SaldoAwalControllerAdmin;
use App\Http\Controllers\admin\NeracaSaldoControllerAdmin;
use App\Http\Controllers\bod\BukuBesarControllerBOD;
use App\Http\Controllers\bod\JurnalingControllerBOD;
use App\Http\Controllers\bod\NeracaSaldoControllerBOD;
use App\Http\Controllers\operator\BukuBesarControllerOperator;
use App\Http\Controllers\operator\CoaControllerOperator;
use App\Http\Controllers\operator\HeaderCoaControllerOperator;
use App\Http\Controllers\operator\HeaderControllerOperator;
use App\Http\Controllers\operator\JurnalingControllerOperator;
use App\Http\Controllers\operator\SaldoAwalControllerOperator;
use App\Http\Controllers\operator\NeracaSaldoControllerOperator;
use App\Http\Controllers\operator\PeriodeControllerOperator;
use App\Http\Middleware\RootSuperuser;
use App\Models\NeracaSaldo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view(Auth::user()->usertype . '.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/logout', [AuthenticatedSessionController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', RootSuperuser::class])->group(function () {
    Route::get('rootsuperuser/dashboard', [HomeController::class, 'homerootsuperuser'])->name('rootsuperuser/dashboard');
    Route::get('/rootsuperuser/products', [ProductControllerRootSuperuser::class, 'index'])->name('rootsuperuser/products');
    Route::get('/rootsuperuser/products/create', [ProductControllerRootSuperuser::class, 'create'])->name('rootsuperuser/products/create');
    Route::post('/rootsuperuser/products/save', [ProductControllerRootSuperuser::class, 'save'])->name('rootsuperuser/products/save');
    Route::get('/rootsuperuser/products/edit/{id}', [ProductControllerRootSuperuser::class, 'edit'])->name('rootsuperuser/products/edit');
    Route::put('/rootsuperuser/products/update/{id}', [ProductControllerRootSuperuser::class, 'update'])->name('rootsuperuser/products/update');
    Route::get('/rootsuperuser/products/delete/{id}', [ProductControllerRootSuperuser::class, 'delete'])->name('rootsuperuser/products/delete');
    Route::get('/rootsuperuser/products/status/{id}', [ProductControllerRootSuperuser::class, 'toggleStatus'])->name('rootsuperuser/products/status');

    Route::get('/rootsuperuser/account/header', [HeaderControllerRootSuperuser::class, 'index'])->name('rootsuperuser/account/header');
    Route::get('/rootsuperuser/account/header/create', [HeaderControllerRootSuperuser::class, 'create'])->name('rootsuperuser/account/header/create');
    Route::post('/rootsuperuser/account/header/save', [HeaderControllerRootSuperuser::class, 'save'])->name('rootsuperuser/account/header/save');
    Route::get('/rootsuperuser/account/header/edit/{id}', [HeaderControllerRootSuperuser::class, 'update'])->name('rootsuperuser/account/header/edit');
    Route::put('/rootsuperuser/account/header/update/{id}', [HeaderControllerRootSuperuser::class, 'updatesave'])->name('rootsuperuser/account/header/update');
    Route::get('/rootsuperuser/account/header/delete/{id}', [HeaderControllerRootSuperuser::class, 'delete'])->name('rootsuperuser/account/header/delete');

    Route::get('/rootsuperuser/account/coa', [CoaControllerRootSuperuser::class, 'index'])->name('rootsuperuser/account/coa');
    Route::get('/rootsuperuser/account/coa/create', [CoaControllerRootSuperuser::class, 'create'])->name('rootsuperuser/account/coa/create');
    Route::post('/rootsuperuser/account/coa/save', [CoaControllerRootSuperuser::class, 'save'])->name('rootsuperuser/account/coa/save');
    Route::get('/rootsuperuser/account/coa/edit/{id}', [CoaControllerRootSuperuser::class, 'update'])->name('rootsuperuser/account/coa/edit');
    Route::put('/rootsuperuser/account/coa/update/{id}', [CoaControllerRootSuperuser::class, 'updatesave'])->name('rootsuperuser/account/coa/update');
    Route::get('/rootsuperuser/account/coa/delete/{id}', [CoaControllerRootSuperuser::class, 'delete'])->name('rootsuperuser/account/coa/delete');

    Route::get('rootsuperuser/account/headercoa', [HeaderCoaControllerRootSuperuser::class, 'index'])->name('rootsuperuser/account/headercoa');

    Route::get('/rootsuperuser/periodes', [PeriodeControllerRootSuperuser::class, 'index'])->name('rootsuperuser/periodes');
    Route::get('/rootsuperuser/periodes/create', [PeriodeControllerRootSuperuser::class, 'create'])->name('rootsuperuser/periodes/create');
    Route::post('/rootsuperuser/periodes/save', [PeriodeControllerRootSuperuser::class, 'save'])->name('rootsuperuser/periodes/save');
    Route::get('/rootsuperuser/periodes/edit/{id}', [PeriodeControllerRootSuperuser::class, 'update'])->name('rootsuperuser/periodes/edit');
    Route::put('/rootsuperuser/periodes/update/{id}', [PeriodeControllerRootSuperuser::class, 'updatesave'])->name('rootsuperuser/periodes/update');
    Route::get('/rootsuperuser/periodes/delete/{id}', [PeriodeControllerRootSuperuser::class, 'delete'])->name('rootsuperuser/periodes/delete');

    Route::get('/rootsuperuser/jurnaling', [JurnalingControllerRootSuperuser::class, 'index'])->name('rootsuperuser/jurnaling');
    Route::get('/rootsuperuser/jurnaling/kaskeluar', [JurnalingControllerRootSuperuser::class, 'indexkaskeluar'])->name('rootsuperuser/jurnaling/kaskeluar');
    Route::get('/rootsuperuser/jurnaling/bankmasuk', [JurnalingControllerRootSuperuser::class, 'indexbankmasuk'])->name('rootsuperuser/jurnaling/bankmasuk');
    Route::get('/rootsuperuser/jurnaling/bankkeluar', [JurnalingControllerRootSuperuser::class, 'indexbankkeluar'])->name('rootsuperuser/jurnaling/bankkeluar');
    Route::get('/rootsuperuser/jurnaling/memorial', [JurnalingControllerRootSuperuser::class, 'indexmemorial'])->name('rootsuperuser/jurnaling/memorial');
    Route::get('/rootsuperuser/jurnaling/memorialpenutup', [JurnalingControllerRootSuperuser::class, 'indexmemorialpenutup'])->name('rootsuperuser/jurnaling/memorialpenutup');
    Route::get('/rootsuperuser/jurnaling/create', [JurnalingControllerRootSuperuser::class, 'create'])->name('rootsuperuser/jurnaling/create');
    Route::post('/rootsuperuser/jurnaling/store', [JurnalingControllerRootSuperuser::class, 'store'])->name('rootsuperuser/jurnaling/store');
    Route::post('/rootsuperuser/jurnaling/storekaskeluar', [JurnalingControllerRootSuperuser::class, 'storekaskeluar'])->name('rootsuperuser/jurnaling/storekaskeluar');
    Route::post('/rootsuperuser/jurnaling/storebankmasuk', [JurnalingControllerRootSuperuser::class, 'storebankmasuk'])->name('rootsuperuser/jurnaling/storebankmasuk');
    Route::post('/rootsuperuser/jurnaling/storebankkeluar', [JurnalingControllerRootSuperuser::class, 'storebankkeluar'])->name('rootsuperuser/jurnaling/storebankkeluar');
    Route::post('/rootsuperuser/jurnaling/storememorial', [JurnalingControllerRootSuperuser::class, 'storememorial'])->name('rootsuperuser/jurnaling/storememorial');
    Route::post('/rootsuperuser/jurnaling/storememorialpenutup', [JurnalingControllerRootSuperuser::class, 'storememorialpenutup'])->name('rootsuperuser/jurnaling/storememorialpenutup');
    Route::post('rootsuperuser/jurnaling/unrekap/{periode_id}', [JurnalingControllerRootSuperuser::class, 'unrekapJurnal'])->name('rootsuperuser/jurnaling/unrekap');
    Route::post('rootsuperuser/jurnaling/rekap/{periode_id}', [JurnalingControllerRootSuperuser::class, 'rekapJurnal'])->name('rootsuperuser/jurnaling/rekap');
    Route::get('/rootsuperuser/jurnaling/showing', [JurnalingControllerRootSuperuser::class, 'showEntries'])->name('rootsuperuser/jurnaling/showing');
    Route::get('/rootsuperuser/jurnaling/months', [JurnalingControllerRootSuperuser::class, 'showMonths'])->name('rootsuperuser/jurnaling/months');

    Route::get('/rootsuperuser/jurnaling/cek-nomor-buktikm', [JurnalingControllerRootSuperuser::class, 'cekNomorBuktiKM'])->name('cekNomorBuktiKM');
    Route::get('/rootsuperuser/jurnaling/cek-nomor-buktikk', [JurnalingControllerRootSuperuser::class, 'cekNomorBuktiKK'])->name('cekNomorBuktiKK');
    Route::get('/rootsuperuser/jurnaling/cek-nomor-buktibm', [JurnalingControllerRootSuperuser::class, 'cekNomorBuktiBM'])->name('cekNomorBuktiBM');
    Route::get('/rootsuperuser/jurnaling/cek-nomor-buktibk', [JurnalingControllerRootSuperuser::class, 'cekNomorBuktiBK'])->name('cekNomorBuktiBK');
    Route::get('/rootsuperuser/jurnaling/cek-nomor-buktimem', [JurnalingControllerRootSuperuser::class, 'cekNomorBuktiMem'])->name('cekNomorBuktiMem');
    Route::get('/rootsuperuser/jurnaling/cek-nomor-buktimempenutup', [JurnalingControllerRootSuperuser::class, 'cekNomorBuktiMemPenutup'])->name('cekNomorBuktiMemPenutup');
    Route::put('/rootsuperuser/jurnaling/editkm/{id}', [JurnalingControllerRootSuperuser::class, 'updatekm'])->name('rootsuperuser/jurnaling/updatekm');
    Route::put('/rootsuperuser/jurnaling/editkk/{id}', [JurnalingControllerRootSuperuser::class, 'updatekk'])->name('rootsuperuser/jurnaling/updatekk');
    Route::put('/rootsuperuser/jurnaling/editbm/{id}', [JurnalingControllerRootSuperuser::class, 'updatebm'])->name('rootsuperuser/jurnaling/updatebm');
    Route::put('/rootsuperuser/jurnaling/editbk/{id}', [JurnalingControllerRootSuperuser::class, 'updatebk'])->name('rootsuperuser/jurnaling/updatebk');
    Route::put('/rootsuperuser/jurnaling/editmem/{id}', [JurnalingControllerRootSuperuser::class, 'updatemem'])->name('rootsuperuser/jurnaling/updatemem');
    Route::put('/rootsuperuser/jurnaling/editmempenutup/{id}', [JurnalingControllerRootSuperuser::class, 'updatemempenutup'])->name('rootsuperuser/jurnaling/updatemempenutup');

    Route::get('/rootsuperuser/bukubesar', [BukuBesarControllerRootSuperuser::class, 'showLedgerForm'])->name('rootsuperuser/bukubesar');
    Route::get('/rootsuperuser/bukubesar/searchCoaByPeriod', [BukuBesarControllerRootSuperuser::class, 'searchCoaByPeriod'])->name('rootsuperuser/bukubesar/searchCoaByPeriod');
    Route::get('rootsuperuser/bukubesar/showAll', [BukuBesarControllerRootSuperuser::class, 'showAll'])->name('rootsuperuser/bukubesar/showAll');
    Route::get('/rootsuperuser/bukubesar/searchCoaByFilter', [BukuBesarControllerRootSuperuser::class, 'searchCoaByFilter'])->name('rootsuperuser/bukubesar/searchCoaByFilter');
    Route::get('/rootsuperuser/bukubesar/searchByDate', [BukuBesarControllerRootSuperuser::class, 'searchByDate'])->name('rootsuperuser/bukubesar/searchByDate');
    Route::get('/rootsuperuser/bukubesar/filter', [BukuBesarControllerRootSuperuser::class, 'filterView'])->name('rootsuperuser/bukubesar/filter');

    Route::get('/rootsuperuser/saldoawal', [SaldoAwalControllerRootSuperuser::class, 'index'])->name('rootsuperuser/saldoawal');
    Route::get('/rootsuperuser/saldoawal/create', [SaldoAwalControllerRootSuperuser::class, 'create'])->name('rootsuperuser/saldoawal/create');
    Route::post('/rootsuperuser/saldoawal/store', [SaldoAwalControllerRootSuperuser::class, 'store'])->name('rootsuperuser/saldoawal/store');
    Route::get('/rootsuperuser/saldoawal/{id}/edit', [SaldoAwalControllerRootSuperuser::class, 'edit'])->name('rootsuperuser.saldoawal.edit');
    Route::put('/rootsuperuser/saldoawal/{id}', [SaldoAwalControllerRootSuperuser::class, 'update'])->name('rootsuperuser.saldoawal.update');
    Route::delete('/rootsuperuser/saldoawal/{id}', [SaldoAwalControllerRootSuperuser::class, 'destroy'])->name('rootsuperuser.saldoawal.destroy');


    Route::get('rootsuperuser/posting', [PostingControlerRootSuperuser::class, 'index'])->name('rootsuperuser/posting');
    Route::post('/rootsuperuser/posting', [PostingControlerRootSuperuser::class, 'postJurnal'])->name('rootsuperuser/posting/post');

    Route::get('/rootsuperuser/neracasaldo/{periode_id}', [NeracaSaldoControllerRootSuperuser::class, 'index'])->name('rootsuperuser/neracasaldo');
    Route::get('/rootsuperuser/neracasaldo/', [NeracaSaldoControllerRootSuperuser::class, 'indexrecap'])->name('rootsuperuser/neracasaldo/');
    Route::get('/rootsuperuser/neracasaldo/showing/{periode_id}', [NeracaSaldoControllerRootSuperuser::class, 'indexmon'])->name('rootsuperuser/neracasaldo/showing');
    Route::get('/rootsuperuser/neracasaldo/months/{periode?}', [JurnalingControllerRootSuperuser::class, 'showPerMonth'])->name('rootsuperuser/neracasaldo/months');
    Route::get('/rootsuperuser/neracasaldo/monthstampil/{periode?}', [NeracaSaldoControllerRootSuperuser::class, 'showPerMonthNeraca'])->name('rootsuperuser/neracasaldo/monthstampil');
    Route::get('/rootsuperuser/neracasaldo/rekap/{periode_id}', [JurnalingControllerRootSuperuser::class, 'rekapJurnalMonth'])->name('rootsuperuser/neracasaldo/rekap');
    Route::get('rootsuperuser/neracasaldo/export/{periode_id}', [NeracaSaldoControllerRootSuperuser::class, 'exportExcel'])->name('rootsuperuser/neracasaldo/export');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('admin/dashboard', [HomeController::class, 'index'])->name('admin/dashboard');
    Route::get('/admin/products', [ProductControllerAdmin::class, 'index'])->name('admin/products');
    Route::get('/admin/products/create', [ProductControllerAdmin::class, 'create'])->name('admin/products/create');
    Route::post('/admin/products/save', [ProductControllerAdmin::class, 'save'])->name('admin/products/save');
    Route::get('/admin/products/edit/{id}', [ProductControllerAdmin::class, 'edit'])->name('admin/products/edit');
    Route::put('/admin/products/update/{id}', [ProductControllerAdmin::class, 'update'])->name('admin/products/update');
    Route::get('/admin/products/delete/{id}', [ProductControllerAdmin::class, 'delete'])->name('admin/products/delete');
    Route::get('/admin/products/status/{id}', [ProductControllerAdmin::class, 'toggleStatus'])->name('admin/products/status');

    Route::get('/admin/periodes', [PeriodeControllerAdmin::class, 'index'])->name('admin/periodes');
    Route::get('/admin/periodes/create', [PeriodeControllerAdmin::class, 'create'])->name('admin/periodes/create');
    Route::post('/admin/periodes/save', [PeriodeControllerAdmin::class, 'save'])->name('admin/periodes/save');
    Route::get('/admin/periodes/edit/{id}', [PeriodeControllerAdmin::class, 'update'])->name('admin/periodes/edit');
    Route::put('/admin/periodes/update/{id}', [PeriodeControllerAdmin::class, 'updatesave'])->name('admin/periodes/update');
    Route::get('/admin/periodes/delete/{id}', [PeriodeControllerAdmin::class, 'delete'])->name('admin/periodes/delete');

    Route::get('/admin/account/header', [HeaderControllerAdmin::class, 'index'])->name('admin/account/header');
    Route::get('/admin/account/header/create', [HeaderControllerAdmin::class, 'create'])->name('admin/account/header/create');
    Route::post('/admin/account/header/save', [HeaderControllerAdmin::class, 'save'])->name('admin/account/header/save');
    Route::get('/admin/account/header/edit/{id}', [HeaderControllerAdmin::class, 'update'])->name('admin/account/header/edit');
    Route::put('/admin/account/header/update/{id}', [HeaderControllerAdmin::class, 'updatesave'])->name('admin/account/header/update');
    Route::get('/admin/account/header/delete/{id}', [HeaderControllerAdmin::class, 'delete'])->name('admin/account/header/delete');

    Route::get('/admin/account/coa', [CoaControllerAdmin::class, 'index'])->name('admin/account/coa');
    Route::get('/admin/account/coa/create', [CoaControllerAdmin::class, 'create'])->name('admin/account/coa/create');
    Route::post('/admin/account/coa/save', [CoaControllerAdmin::class, 'save'])->name('admin/account/coa/save');
    Route::get('/admin/account/coa/edit/{id}', [CoaControllerAdmin::class, 'update'])->name('admin/account/coa/edit');
    Route::put('/admin/account/coa/update/{id}', [CoaControllerAdmin::class, 'updatesave'])->name('admin/account/coa/update');
    Route::get('/admin/account/coa/delete/{id}', [CoaControllerAdmin::class, 'delete'])->name('admin/account/coa/delete');

    Route::get('admin/account/headercoa', [HeaderCoaControllerAdmin::class, 'index'])->name('admin/account/headercoa');

    Route::get('/admin/jurnaling', [JurnalingControllerAdmin::class, 'index'])->name('admin/jurnaling');
    Route::get('/admin/jurnaling/kaskeluar', [JurnalingControllerAdmin::class, 'indexkaskeluar'])->name('admin/jurnaling/kaskeluar');
    Route::get('/admin/jurnaling/bankmasuk', [JurnalingControllerAdmin::class, 'indexbankmasuk'])->name('admin/jurnaling/bankmasuk');
    Route::get('/admin/jurnaling/bankkeluar', [JurnalingControllerAdmin::class, 'indexbankkeluar'])->name('admin/jurnaling/bankkeluar');
    Route::get('/admin/jurnaling/memorial', [JurnalingControllerAdmin::class, 'indexmemorial'])->name('admin/jurnaling/memorial');
    Route::get('/admin/jurnaling/memorialpenutup', [JurnalingControllerAdmin::class, 'indexmemorialpenutup'])->name('admin/jurnaling/memorialpenutup');
    Route::get('/admin/jurnaling/create', [JurnalingControllerAdmin::class, 'create'])->name('admin/jurnaling/create');
    Route::post('/admin/jurnaling/store', [JurnalingControllerAdmin::class, 'store'])->name('admin/jurnaling/store');
    Route::post('/admin/jurnaling/storekaskeluar', [JurnalingControllerAdmin::class, 'storekaskeluar'])->name('admin/jurnaling/storekaskeluar');
    Route::post('/admin/jurnaling/storebankmasuk', [JurnalingControllerAdmin::class, 'storebankmasuk'])->name('admin/jurnaling/storebankmasuk');
    Route::post('/admin/jurnaling/storebankkeluar', [JurnalingControllerAdmin::class, 'storebankkeluar'])->name('admin/jurnaling/storebankkeluar');
    Route::post('/admin/jurnaling/storememorial', [JurnalingControllerAdmin::class, 'storememorial'])->name('admin/jurnaling/storememorial');
    Route::post('/admin/jurnaling/storememorialpenutup', [JurnalingControllerAdmin::class, 'storememorialpenutup'])->name('admin/jurnaling/storememorialpenutup');
    Route::post('admin/jurnaling/unrekap/{periode_id}', [JurnalingControllerAdmin::class, 'unrekapJurnal'])->name('admin/jurnaling/unrekap');
    Route::post('admin/jurnaling/rekap/{periode_id}', [JurnalingControllerAdmin::class, 'rekapJurnal'])->name('admin/jurnaling/rekap');
    Route::get('/admin/jurnaling/showing', [JurnalingControllerAdmin::class, 'showEntries'])->name('admin/jurnaling/showing');
    Route::get('/admin/jurnaling/months', [JurnalingControllerAdmin::class, 'showMonths'])->name('admin/jurnaling/months');

    Route::get('/admin/jurnaling/cek-nomor-buktikm', [JurnalingControllerAdmin::class, 'cekNomorBuktiKM'])->name('cekNomorBuktiKM');
    Route::get('/admin/jurnaling/cek-nomor-buktikk', [JurnalingControllerAdmin::class, 'cekNomorBuktiKK'])->name('cekNomorBuktiKK');
    Route::get('/admin/jurnaling/cek-nomor-buktibm', [JurnalingControllerAdmin::class, 'cekNomorBuktiBM'])->name('cekNomorBuktiBM');
    Route::get('/admin/jurnaling/cek-nomor-buktibk', [JurnalingControllerAdmin::class, 'cekNomorBuktiBK'])->name('cekNomorBuktiBK');
    Route::get('/admin/jurnaling/cek-nomor-buktimem', [JurnalingControllerAdmin::class, 'cekNomorBuktiMem'])->name('cekNomorBuktiMem');
    Route::get('/admin/jurnaling/cek-nomor-buktimempenutup', [JurnalingControllerAdmin::class, 'cekNomorBuktiMemPenutup'])->name('cekNomorBuktiMemPenutup');
    Route::put('/admin/jurnaling/editkm/{id}', [JurnalingControllerAdmin::class, 'updatekm'])->name('admin/jurnaling/updatekm');
    Route::put('/admin/jurnaling/editkk/{id}', [JurnalingControllerAdmin::class, 'updatekk'])->name('admin/jurnaling/updatekk');
    Route::put('/admin/jurnaling/editbm/{id}', [JurnalingControllerAdmin::class, 'updatebm'])->name('admin/jurnaling/updatebm');
    Route::put('/admin/jurnaling/editbk/{id}', [JurnalingControllerAdmin::class, 'updatebk'])->name('admin/jurnaling/updatebk');
    Route::put('/admin/jurnaling/editmem/{id}', [JurnalingControllerAdmin::class, 'updatemem'])->name('admin/jurnaling/updatemem');
    Route::put('/admin/jurnaling/editmempenutup/{id}', [JurnalingControllerAdmin::class, 'updatemempenutup'])->name('admin/jurnaling/updatemempenutup');

    Route::get('/admin/bukubesar', [BukuBesarControllerAdmin::class, 'showLedgerForm'])->name('admin/bukubesar');
    Route::get('/admin/bukubesar/searchCoaByPeriod', [BukuBesarControllerAdmin::class, 'searchCoaByPeriod'])->name('admin/bukubesar/searchCoaByPeriod');
    Route::get('admin/bukubesar/showAll', [BukuBesarControllerAdmin::class, 'showAll'])->name('admin/bukubesar/showAll');
    Route::get('/admin/bukubesar/searchCoaByFilter', [BukuBesarControllerAdmin::class, 'searchCoaByFilter'])->name('admin/bukubesar/searchCoaByFilter');
    Route::get('/admin/bukubesar/searchByDate', [BukuBesarControllerAdmin::class, 'searchByDate'])->name('admin/bukubesar/searchByDate');
    Route::get('/admin/bukubesar/filter', [BukuBesarControllerAdmin::class, 'filterView'])->name('admin/bukubesar/filter');

    Route::get('/admin/saldoawal', [SaldoAwalControllerAdmin::class, 'index'])->name('admin/saldoawal');
    Route::get('/admin/saldoawal/create', [SaldoAwalControllerAdmin::class, 'create'])->name('admin/saldoawal/create');
    Route::post('/admin/saldoawal/store', [SaldoAwalControllerAdmin::class, 'store'])->name('admin/saldoawal/store');
    Route::get('/admin/saldoawal/{id}/edit', [SaldoAwalControllerAdmin::class, 'edit'])->name('admin.saldoawal.edit');
    Route::put('/admin/saldoawal/{id}', [SaldoAwalControllerAdmin::class, 'update'])->name('admin.saldoawal.update');
    Route::delete('/admin/saldoawal/{id}', [SaldoAwalControllerAdmin::class, 'destroy'])->name('admin.saldoawal.destroy');

    Route::get('/admin/neracasaldo/{periode_id}', [NeracaSaldoControllerAdmin::class, 'index'])->name('admin/neracasaldo');
    Route::get('/admin/neracasaldo/', [NeracaSaldoControllerAdmin::class, 'indexrecap'])->name('admin/neracasaldo/');
    Route::get('/admin/neracasaldo/showing/{periode_id}', [NeracaSaldoControllerAdmin::class, 'indexmon'])->name('admin/neracasaldo/showing');
    Route::get('/admin/neracasaldo/months/{periode?}', [JurnalingControllerAdmin::class, 'showPerMonth'])->name('admin/neracasaldo/months');
    Route::get('/admin/neracasaldo/monthstampil/{periode?}', [NeracaSaldoControllerAdmin::class, 'showPerMonthNeraca'])->name('admin/neracasaldo/monthstampil');
    Route::get('/admin/neracasaldo/rekap/{periode_id}', [JurnalingControllerAdmin::class, 'rekapJurnalMonth'])->name('admin/neracasaldo/rekap');
    Route::get('admin/neracasaldo/export/{periode_id}', [NeracaSaldoControllerAdmin::class, 'exportExcel'])->name('admin/neracasaldo/export');
});

Route::middleware(['auth', 'operator'])->group(function () {

    Route::get('operator/dashboard', [HomeController::class, 'homeOperator'])->name('operator/dashboard');

    Route::get('/operator/periodes', [PeriodeControllerOperator::class, 'index'])->name('operator/periodes');
    Route::get('/operator/periodes/create', [PeriodeControllerOperator::class, 'create'])->name('operator/periodes/create');
    Route::post('/operator/periodes/save', [PeriodeControllerOperator::class, 'save'])->name('operator/periodes/save');
    Route::get('/operator/periodes/edit/{id}', [PeriodeControllerOperator::class, 'update'])->name('operator/periodes/edit');
    Route::put('/operator/periodes/update/{id}', [PeriodeControllerOperator::class, 'updatesave'])->name('operator/periodes/update');
    Route::get('/operator/periodes/delete/{id}', [PeriodeControllerOperator::class, 'delete'])->name('operator/periodes/delete');

    Route::get('/operator/account/header', [HeaderControllerOperator::class, 'index'])->name('operator/account/header');
    Route::get('/operator/account/header/create', [HeaderControllerOperator::class, 'create'])->name('operator/account/header/create');
    Route::post('/operator/account/header/save', [HeaderControllerOperator::class, 'save'])->name('operator/account/header/save');
    Route::get('/operator/account/header/edit/{id}', [HeaderControllerOperator::class, 'update'])->name('operator/account/header/edit');
    Route::put('/operator/account/header/update/{id}', [HeaderControllerOperator::class, 'updatesave'])->name('operator/account/header/update');
    Route::get('/operator/account/header/delete/{id}', [HeaderControllerOperator::class, 'delete'])->name('operator/account/header/delete');

    Route::get('/operator/account/coa', [CoaControllerOperator::class, 'index'])->name('operator/account/coa');
    Route::get('/operator/account/coa/create', [CoaControllerOperator::class, 'create'])->name('operator/account/coa/create');
    Route::post('/operator/account/coa/save', [CoaControllerOperator::class, 'save'])->name('operator/account/coa/save');
    Route::get('/operator/account/coa/edit/{id}', [CoaControllerOperator::class, 'update'])->name('operator/account/coa/edit');
    Route::put('/operator/account/coa/update/{id}', [CoaControllerOperator::class, 'updatesave'])->name('operator/account/coa/update');
    Route::get('/operator/account/coa/delete/{id}', [CoaControllerOperator::class, 'delete'])->name('operator/account/coa/delete');

    Route::get('operator/account/headercoa', [HeaderCoaControllerOperator::class, 'index'])->name('operator/account/headercoa');

    Route::get('/operator/jurnaling', [JurnalingControllerOperator::class, 'index'])->name('operator/jurnaling');
    Route::get('/operator/jurnaling/kaskeluar', [JurnalingControllerOperator::class, 'indexkaskeluar'])->name('operator/jurnaling/kaskeluar');
    Route::get('/operator/jurnaling/bankmasuk', [JurnalingControllerOperator::class, 'indexbankmasuk'])->name('operator/jurnaling/bankmasuk');
    Route::get('/operator/jurnaling/bankkeluar', [JurnalingControllerOperator::class, 'indexbankkeluar'])->name('operator/jurnaling/bankkeluar');
    Route::get('/operator/jurnaling/memorial', [JurnalingControllerOperator::class, 'indexmemorial'])->name('operator/jurnaling/memorial');
    Route::get('/operator/jurnaling/memorialpenutup', [JurnalingControllerOperator::class, 'indexmemorialpenutup'])->name('operator/jurnaling/memorialpenutup');
    Route::get('/operator/jurnaling/create', [JurnalingControllerOperator::class, 'create'])->name('operator/jurnaling/create');
    Route::post('/operator/jurnaling/store', [JurnalingControllerOperator::class, 'store'])->name('operator/jurnaling/store');
    Route::post('/operator/jurnaling/storekaskeluar', [JurnalingControllerOperator::class, 'storekaskeluar'])->name('operator/jurnaling/storekaskeluar');
    Route::post('/operator/jurnaling/storebankmasuk', [JurnalingControllerOperator::class, 'storebankmasuk'])->name('operator/jurnaling/storebankmasuk');
    Route::post('/operator/jurnaling/storebankkeluar', [JurnalingControllerOperator::class, 'storebankkeluar'])->name('operator/jurnaling/storebankkeluar');
    Route::post('/operator/jurnaling/storememorial', [JurnalingControllerOperator::class, 'storememorial'])->name('operator/jurnaling/storememorial');
    Route::post('/operator/jurnaling/storememorialpenutup', [JurnalingControllerOperator::class, 'storememorialpenutup'])->name('operator/jurnaling/storememorialpenutup');
    Route::post('operator/jurnaling/unrekap/{periode_id}', [JurnalingControllerOperator::class, 'unrekapJurnal'])->name('operator/jurnaling/unrekap');
    Route::post('operator/jurnaling/rekap/{periode_id}', [JurnalingControllerOperator::class, 'rekapJurnal'])->name('operator/jurnaling/rekap');
    Route::get('/operator/jurnaling/showing', [JurnalingControllerOperator::class, 'showEntries'])->name('operator/jurnaling/showing');
    Route::get('/operator/jurnaling/months', [JurnalingControllerOperator::class, 'showMonths'])->name('operator/jurnaling/months');

    Route::get('/operator/jurnaling/cek-nomor-buktikm', [JurnalingControllerOperator::class, 'cekNomorBuktiKM'])->name('cekNomorBuktiKM');
    Route::get('/operator/jurnaling/cek-nomor-buktikk', [JurnalingControllerOperator::class, 'cekNomorBuktiKK'])->name('cekNomorBuktiKK');
    Route::get('/operator/jurnaling/cek-nomor-buktibm', [JurnalingControllerOperator::class, 'cekNomorBuktiBM'])->name('cekNomorBuktiBM');
    Route::get('/operator/jurnaling/cek-nomor-buktibk', [JurnalingControllerOperator::class, 'cekNomorBuktiBK'])->name('cekNomorBuktiBK');
    Route::get('/operator/jurnaling/cek-nomor-buktimem', [JurnalingControllerOperator::class, 'cekNomorBuktiMem'])->name('cekNomorBuktiMem');
    Route::get('/operator/jurnaling/cek-nomor-buktimempenutup', [JurnalingControllerOperator::class, 'cekNomorBuktiMemPenutup'])->name('cekNomorBuktiMemPenutup');

    Route::put('/operator/jurnaling/editkm/{id}', [JurnalingControllerOperator::class, 'updatekm'])->name('operator/jurnaling/updatekm');
    Route::put('/operator/jurnaling/editkk/{id}', [JurnalingControllerOperator::class, 'updatekk'])->name('operator/jurnaling/updatekk');
    Route::put('/operator/jurnaling/editbm/{id}', [JurnalingControllerOperator::class, 'updatebm'])->name('operator/jurnaling/updatebm');
    Route::put('/operator/jurnaling/editbk/{id}', [JurnalingControllerOperator::class, 'updatebk'])->name('operator/jurnaling/updatebk');
    Route::put('/operator/jurnaling/editmem/{id}', [JurnalingControllerOperator::class, 'updatemem'])->name('operator/jurnaling/updatemem');
    Route::put('/operator/jurnaling/editmempenutup/{id}', [JurnalingControllerOperator::class, 'updatemempenutup'])->name('operator/jurnaling/updatemempenutup');

    Route::get('/operator/bukubesar', [BukuBesarControllerOperator::class, 'showLedgerForm'])->name('operator/bukubesar');
    Route::get('/operator/bukubesar/searchCoaByPeriod', [BukuBesarControllerOperator::class, 'searchCoaByPeriod'])->name('operator/bukubesar/searchCoaByPeriod');
    Route::get('operator/bukubesar/showAll', [BukuBesarControllerOperator::class, 'showAll'])->name('operator/bukubesar/showAll');
    Route::get('/operator/bukubesar/searchCoaByFilter', [BukuBesarControllerOperator::class, 'searchCoaByFilter'])->name('operator/bukubesar/searchCoaByFilter');
    Route::get('/operator/bukubesar/searchByDate', [BukuBesarControllerOperator::class, 'searchByDate'])->name('operator/bukubesar/searchByDate');
    Route::get('/operator/bukubesar/filter', [BukuBesarControllerOperator::class, 'filterView'])->name('operator/bukubesar/filter');

    Route::get('/operator/saldoawal', [SaldoAwalControllerOperator::class, 'index'])->name('operator/saldoawal');
    Route::get('/operator/saldoawal/create', [SaldoAwalControllerOperator::class, 'create'])->name('operator/saldoawal/create');
    Route::post('/operator/saldoawal/store', [SaldoAwalControllerOperator::class, 'store'])->name('operator/saldoawal/store');
    Route::get('/operator/saldoawal/{id}/edit', [SaldoAwalControllerOperator::class, 'edit'])->name('operator.saldoawal.edit');
    Route::put('/operator/saldoawal/{id}', [SaldoAwalControllerOperator::class, 'update'])->name('operator.saldoawal.update');
    Route::delete('/operator/saldoawal/{id}', [SaldoAwalControllerOperator::class, 'destroy'])->name('operator.saldoawal.destroy');

    Route::get('/operator/neracasaldo/{periode_id}', [NeracaSaldoControllerOperator::class, 'index'])->name('operator/neracasaldo');
    Route::get('/operator/neracasaldo/', [NeracaSaldoControllerOperator::class, 'indexrecap'])->name('operator/neracasaldo/');
    Route::get('/operator/neracasaldo/showing/{periode_id}', [NeracaSaldoControllerOperator::class, 'indexmon'])->name('operator/neracasaldo/showing');
    Route::get('/operator/neracasaldo/months/{periode?}', [JurnalingControllerOperator::class, 'showPerMonth'])->name('operator/neracasaldo/months');
    Route::get('/operator/neracasaldo/monthstampil/{periode?}', [NeracaSaldoControllerOperator::class, 'showPerMonthNeraca'])->name('operator/neracasaldo/monthstampil');
    Route::get('/operator/neracasaldo/rekap/{periode_id}', [JurnalingControllerOperator::class, 'rekapJurnalMonth'])->name('operator/neracasaldo/rekap');
    Route::get('operator/neracasaldo/export/{periode_id}', [NeracaSaldoControllerOperator::class, 'exportExcel'])->name('operator/neracasaldo/export');
});


Route::middleware(['auth', 'bod'])->group(function () {

    Route::get('bod/dashboard', [HomeController::class, 'homeBod'])->name('bod/dashboard');;

    Route::get('/bod/jurnaling/showing', [JurnalingControllerBOD::class, 'showEntries'])->name('bod/jurnaling/showing');
    Route::get('/bod/jurnaling/months', [JurnalingControllerBOD::class, 'showMonths'])->name('bod/jurnaling/months');

    Route::get('/bod/bukubesar', [BukuBesarControllerBOD::class, 'showLedgerForm'])->name('bod/bukubesar');
    Route::get('/bod/bukubesar/searchCoaByPeriod', [BukuBesarControllerBOD::class, 'searchCoaByPeriod'])->name('bod/bukubesar/searchCoaByPeriod');
    Route::get('bod/bukubesar/showAll', [BukuBesarControllerBOD::class, 'showAll'])->name('bod/bukubesar/showAll');
    Route::get('/bod/bukubesar/searchCoaByFilter', [BukuBesarControllerBOD::class, 'searchCoaByFilter'])->name('bod/bukubesar/searchCoaByFilter');
    Route::get('/bod/bukubesar/searchByDate', [BukuBesarControllerBOD::class, 'searchByDate'])->name('bod/bukubesar/searchByDate');
    Route::get('/bod/bukubesar/filter', [BukuBesarControllerBOD::class, 'filterView'])->name('bod/bukubesar/filter');

    Route::get('/bod/neracasaldo/{periode_id}', [NeracaSaldoControllerBOD::class, 'index'])->name('bod/neracasaldo');
    Route::get('/bod/neracasaldo/', [NeracaSaldoControllerBOD::class, 'indexrecap'])->name('bod/neracasaldo/');
    Route::get('/bod/neracasaldo/showing/{periode_id}', [NeracaSaldoControllerBOD::class, 'indexmon'])->name('bod/neracasaldo/showing');
    Route::get('/bod/neracasaldo/months/{periode?}', [JurnalingControllerBOD::class, 'showPerMonth'])->name('bod/neracasaldo/months');
    Route::get('/bod/neracasaldo/monthstampil/{periode?}', [NeracaSaldoControllerBOD::class, 'showPerMonthNeraca'])->name('bod/neracasaldo/monthstampil');
    Route::get('/bod/neracasaldo/rekap/{periode_id}', [JurnalingControllerBOD::class, 'rekapJurnalMonth'])->name('bod/neracasaldo/rekap');
    Route::get('bod/neracasaldo/export/{periode_id}', [NeracaSaldoControllerBOD::class, 'exportExcel'])->name('bod/neracasaldo/export');
});


require __DIR__ . '/auth.php';
