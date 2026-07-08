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
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div data-i18n="Analytics">User Management</div>
            </a>
        </li>
        <li class="menu-item">
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
                <li class="menu-item">
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
        <li class="menu-item active">
            <a href="{{ route('admin/jurnaling') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div data-i18n="Analytics">Jurnaling</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('admin/bukubesar') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div data-i18n="Analytics">Buku Besar</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('admin/saldoawal') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div data-i18n="Analytics">Saldo Awal</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('admin/dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div data-i18n="Analytics">Neraca Saldo</div>
            </a>
        </li>

    </ul>
</aside>

<!-- Content wrapper -->
<div class="content-wrapper">
  <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
      <div class="shadow-sm card sm:rounded-lg">
        <div class="text-gray-900 card-body">
          <h1 class="mb-4">Add Periode</h1>
          <hr />
          @if (session()->has('error'))
            <div class="alert alert-danger">
              {{ session('error') }}
            </div>
          @endif
          <p><a href="{{ route('admin/jurnaling') }}" class="mb-4 btn btn-primary">Go Back</a></p>

          <form action="{{ route('admin/jurnaling/save') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
              <label for="nama_periode" class="form-label">Nama Periode</label>
              <input type="text" id="nama_periode" name="nama_periode" class="form-control @error('nama_periode') is-invalid @enderror" placeholder="Enter periode name" value="{{ old('nama_periode') }}">
              @error('nama_periode')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="mb-3">
              <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
              <input type="date" id="tanggal_awal" name="tanggal_awal" class="form-control @error('tanggal_awal') is-invalid @enderror" value="{{ old('tanggal_awal') }}">
              @error('tanggal_awal')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="mb-3">
              <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
              <input type="date" id="tanggal_akhir" name="tanggal_akhir" class="form-control @error('tanggal_akhir') is-invalid @enderror" value="{{ old('tanggal_akhir') }}">
              @error('tanggal_akhir')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="mb-3">
              <button type="submit" class="btn btn-primary">Submit</button>
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
