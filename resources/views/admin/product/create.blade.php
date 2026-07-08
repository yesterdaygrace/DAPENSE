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
    <li class="menu-item active">
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
    <li class="menu-item">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
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
<!-- / Menu -->

<!-- Content wrapper -->
<div class="content-wrapper">
  <!-- Content -->
  <div class="py-12">
    <div class="container-xxl flex-grow-1 container-p-y">
      <div class="card shadow-sm sm:rounded-lg">
        <div class="card-body text-gray-900">
          <h1 class="mb-4">Tambah User</h1>
          <hr />
          @if (session()->has('error'))
          <div class="alert alert-danger">
            {{ session('error') }}
          </div>
          @endif
          <p><a href="{{ route('admin/products') }}" class="btn btn-primary mb-4">Kembali</a></p>

          <form action="{{ route('admin/products/save') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
              <label for="name" class="form-label">Nama</label>
              <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Masukkan nama" value="{{ old('name') }}">
              @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Masukkan email" value="{{ old('email') }}">
              @error('email')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="mb-3">
              <label for="usertype" class="form-label">User Type</label>
              <select id="usertype" name="usertype" class="form-control @error('usertype') is-invalid @enderror">
                <option value="">Select User Type</option>
                <option value="operator" {{ old('usertype') == 'operator' ? 'selected' : '' }}>Operator</option>
                <option value="bod" {{ old('usertype') == 'bod' ? 'selected' : '' }}>BOD</option>
              </select>
              @error('usertype')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan password">
              @error('password')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="mb-3">
              <label for="password_confirmation" class="form-label">Confirm Password</label>
              <input type="password" id="password_confirmation" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Konfirmasi password">
              @error('password_confirmation')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="mb-3">
              <label for="image" class="form-label">Foto Profil</label>
              <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror">
              @error('image')
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
  </div>
  <!-- / Content -->

  <div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->
@endsection