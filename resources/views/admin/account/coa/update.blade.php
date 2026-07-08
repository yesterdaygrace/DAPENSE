@extends('layouts.applayout')
@section('content')
<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('admin/dashboard') }}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder ms-2">{{ Auth::user()->name }}</span>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>
    <ul class="py-1 menu-inner">
        <!-- Dashboard -->
        <li class="menu-item">
            <a href="{{ route('admin/dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('admin/products') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Analytics">User Management</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('admin/periodes') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calendar"></i>
                <div data-i18n="Analytics">Periode</div>
            </a>
        </li>
        <li class="menu-item active open">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div data-i18n="Layouts">Accounts</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('admin/account/header') }}" class="menu-link">
                        <div data-i18n="Without menu">Header</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item active">
                    <a href="{{ route('admin/account/coa') }}" class="menu-link">
                        <div data-i18n="Without menu">COA</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('admin/account/headercoa') }}" class="menu-link">
                        <div data-i18n="Without menu">Combine Header & COA</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="{{ route('admin/saldoawal') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-money"></i>
                <div data-i18n="Analytics">Saldo Awal</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-notepad"></i>
                <div data-i18n="Layouts">Jurnaling</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('admin/jurnaling') }}" class="menu-link">
                        <div data-i18n="Without menu">Kas Masuk</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('admin/jurnaling/kaskeluar') }}" class="menu-link">
                        <div data-i18n="Without menu">Kas Keluar</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('admin/jurnaling/bankmasuk') }}" class="menu-link">
                        <div data-i18n="Without menu">Bank Masuk</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('admin/jurnaling/bankkeluar') }}" class="menu-link">
                        <div data-i18n="Without menu">Bank Keluar</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('admin/jurnaling/memorial') }}" class="menu-link">
                        <div data-i18n="Without menu">Memorial</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('admin/jurnaling/memorialpenutup') }}" class="menu-link">
                        <div data-i18n="Without menu">Memorial (Penutup)</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('admin/jurnaling/showing') }}" class="menu-link">
                        <div data-i18n="Without menu">Tampil</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="{{ route('admin/bukubesar') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-book"></i>
                <div data-i18n="Analytics">Buku Besar</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('admin/neracasaldo/') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calculator"></i>
                <div data-i18n="Analytics">Neraca Saldo</div>
            </a>
        </li>
    </ul>
</aside>
<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit COA</h5>
            </div>
            <div class="card-body">
                @if(Session::has('success'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('success') }}
                </div>
                @endif
                <form action="{{ route('admin/account/coa/update', $coa->id) }}" style="text-transform: uppercase;" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="kode_akun" class="form-label">Kode Akun</label>
                        <input style="text-transform: uppercase;" type="text" id="kode_akun" name="kode_akun" minlength="8" maxlength="8" class="form-control @error('kode_akun') is-invalid @enderror" placeholder="Masukkan kode akun" value="{{ old('kode_akun', $coa->kode_akun) }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        @error('kode_akun')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="nama_akun" class="form-label">Nama Akun</label>
                        <input style="text-transform: uppercase;" type="text" id="nama_akun" name="nama_akun" class="form-control @error('nama_akun') is-invalid @enderror" placeholder="Masukkan nama akun" value="{{ old('nama_akun', $coa->nama_akun) }}">
                        @error('nama_akun')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="saldo_normal" class="form-label">Saldo Normal</label>
                        <select id="saldo_normal" name="saldo_normal" class="form-control @error('saldo_normal') is-invalid @enderror">
                            <option value="">PILIH</option>
                            <option value="Debit" {{ old('saldo_normal') == 'DEBIT' ? 'selected' : '' }}>Debit</option>
                            <option value="Kredit" {{ old('saldo_normal') == 'KREDIT' ? 'selected' : '' }}>Kredit</option>
                        </select>
                        @error('saldo_normal')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori</label>
                        <select style="text-transform: uppercase;" type="text" id="kategori" name="kategori" class="form-control @error('kategori') is-invalid @enderror" placeholder="Enter kategori" value="{{ old('kategori') }}">
                            <option value="Aktiva">Aktiva</option>
                            <option value="Kewajiban">Kewajiban</option>
                            <option value="Beban">Beban</option>
                            <option value="Pendapatan">Pendapatan</option>
                        </select> @error('kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="level" class="form-label">Level</label>
                        <select id="level" name="level" class="form-select @error('level') is-invalid @enderror" required>
                            <option value="4" {{ old('level', $coa->level) == '4' ? 'selected' : '' }}>4</option>
                        </select>
                        @error('level')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="header_coa_id" class="form-label">Header COA</label>
                        <select id="header_coa_id" name="header_coa_id" class="form-control @error('header_coa_id') is-invalid @enderror">
                            @foreach($headers as $header)
                            <option value="{{ $header->id }}" {{ old('header_coa_id', $coa->header_coa_id) == $header->id ? 'selected' : '' }}>
                                {{ $header->kode_header }} - {{ $header->nama_header }}
                            </option>
                            @endforeach
                        </select>
                        @error('header_coa_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- / Content -->
    <div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->
@endsection