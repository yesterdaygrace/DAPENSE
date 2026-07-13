@extends('layouts.applayout')
@section('content')
@include('components.admin-sidebar', ['activeMenu' => 'account-coa'])

<<!-- Content wrapper -->
  <div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="mb-0">List COA</h5>
          <a href="{{ route('admin/account/coa/create') }}" class="btn btn-primary">Tambah COA</a>
        </div>

        <div class="card-header d-flex align-items-center justify-content-between">
          <div class="input-group input-group-merge">
            <span class="input-group-text" id="basic-addon-search31"><i class="bx bx-search"></i></span>
            <input type="text" id="search-field" class="form-control" placeholder="Cari Kode COA atau Nama COA">
          </div>
        </div>

        <div class="card-body">
          @if(Session::has('success'))
          <div class="alert alert-success" role="alert">
            {{ Session::get('success') }}
          </div>
          @endif
          <table class="table table-hover" id="coa-table">
            <thead class="table-primary">
              <tr>
                <th>Kode Akun</th>
                <th>Nama Akun</th>
                <th>Saldo Normal</th>
                <th>Kategori</th>
                <th>Level</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($coas as $coa)
              <tr>
                <td class="align-middle">{{ $coa->kode_akun }}</td>
                <td style="text-transform: uppercase;" class="align-middle">{{ $coa->nama_akun }}</td>
                <td class="align-middle">{{ $coa->saldo_normal }}</td>
                <td style="text-transform: uppercase;" class="align-middle">{{ $coa->kategori }}</td>
                <td class="align-middle">{{ $coa->level }}</td>
                <td class="align-middle">
                  <a href="{{ route('admin/account/coa/edit', $coa->id) }}" type="button" class="btn btn-warning">Edit</a>
                  <button type="button" class="btn btn-danger" onclick="confirmDelete('{{ route('admin/account/coa/delete', $coa->id) }}')">Hapus</button>
                </td>
              </tr>
              @empty
              <tr>
                <td class="text-center" colspan="7">No COA found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <!-- / Content -->
  <div class="content-backdrop fade"></div>

  <!-- Toast Confirm Delete -->
  <div class="p-3 toast-container position-fixed top-50 start-50 translate-middle" style="z-index: 1050;">
    <div id="deleteToast" class="text-white toast bg-warning" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header">
        <i class="bx bx-bell me-2"></i>
        <strong class="me-auto">Konfirmasi Hapus</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      <div class="toast-body">
        Apakah Anda yakin ingin menghapus COA ini?
        <div class="pt-2 mt-4 d-flex justify-content-end border-top">
          <button type="button" class="btn btn-light btn-sm me-2" data-bs-dismiss="toast">Batal</button>
          <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn">Hapus</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.getElementById('search-field').addEventListener('keyup', function() {
      let searchQuery = this.value.toLowerCase();
      let tableRows = document.querySelectorAll('#coa-table tbody tr');

      tableRows.forEach(row => {
        // Get values from 'Kode Akun' and 'Nama Akun' columns
        let kodeAkun = row.cells[0].textContent.toLowerCase();
        let namaAkun = row.cells[1].textContent.toLowerCase();

        // Check if either field includes the search query
        if (kodeAkun.includes(searchQuery) || namaAkun.includes(searchQuery)) {
          row.style.display = ''; // Show row
        } else {
          row.style.display = 'none'; // Hide row
        }
      });
    });

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