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
    <li class="menu-item active">
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
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">List Periode</h5>
        <a href="{{ route('admin/periodes/create') }}" class="btn btn-primary">Tambah Periode</a>
      </div>
      <div class="card-body">
        @if(Session::has('success'))
        <div class="alert alert-success" role="alert">
          {{ Session::get('success') }}
        </div>
        @endif
        @if(Session::has('error'))
        <div class="alert alert-danger" role="alert">
          {{ Session::get('error') }}
        </div>
        @endif
        <table class="table table-hover">
          <thead class="table-primary">
            <tr>
              <th>#</th>
              <th>Nama</th>
              <th>Tanggal Awal</th>
              <th>Tanggal Akhir</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($periodes as $periode)
            <tr>
              <td class="align-middle">{{ $loop->iteration }}</td>
              <td class="align-middle">{{ $periode->nama_periode }}</td>
              <td class="align-middle">{{ $periode->tanggal_awal }}</td>
              <td class="align-middle">{{ $periode->tanggal_akhir }}</td>
              <td class="align-middle">
                <!-- <a href="{{ route('admin/periodes/edit', $periode->id) }}" type="button" class="btn btn-warning">Edit</a> -->
                <button type="button" class="btn btn-danger" onclick="confirmDelete('{{ route('admin/periodes/delete', $periode->id) }}')">Hapus</button>
              </td>
            </tr>
            @empty
            <tr>
              <td class="text-center" colspan="5">No periode found</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- / Content -->
  <div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->

<!-- Toast Confirm Delete -->
<div class="toast-container position-fixed top-50 start-50 translate-middle p-3" style="z-index: 1050;">
  <div id="deleteToast" class="toast bg-warning text-white" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <i class="bx bx-bell me-2"></i>
      <strong class="me-auto">Konfirmasi Hapus</strong>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
      Apakah Anda yakin ingin menghapus periode ini?
      <div class="d-flex justify-content-end mt-4 pt-2 border-top">
        <button type="button" class="btn btn-light btn-sm me-2" data-bs-dismiss="toast">Batal</button>
        <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn">Hapus</button>
      </div>
    </div>
  </div>
</div>

<script>
  let deleteUrl = '';

  function confirmDelete(url) {
    deleteUrl = url;
    var toastEl = document.getElementById('deleteToast');
    var toast = new bootstrap.Toast(toastEl);
    toast.show();
  }

  document.getElementById('confirmDeleteBtn').onclick = function() {
    if (deleteUrl) {
      window.location.href = deleteUrl;
    }
  };
</script>

@endsection