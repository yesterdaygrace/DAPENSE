@extends('layouts.applayout')
@section('content')

@include('components.admin-sidebar', ['activeMenu' => 'periode'])

<!-- Content wrapper -->
<div class="content-wrapper">
  <!-- Content -->
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">List Periode</h5>
        <a href="{{ route('operator/periodes/create') }}" class="btn btn-primary">Tambah Periode</a>
      </div>
      <div class="card-body">
        @if(Session::has('success'))
        <div class="alert alert-success" role="alert">
          {{ Session::get('success') }}
        </div>
        @endif
        <table class="table table-hover">
          <thead class="table-primary">
            <tr>
              <th>#</th>
              <th>Nama</th>
              <th>Tanggal Awal</th>
              <th>Tanggal Akhir</th>
              <!-- <th>Action</th> -->
            </tr>
          </thead>
          <tbody>
            @forelse ($periodes as $periode)
            <tr>
              <td class="align-middle">{{ $loop->iteration }}</td>
              <td class="align-middle">{{ $periode->nama_periode }}</td>
              <td class="align-middle">{{ $periode->tanggal_awal }}</td>
              <td class="align-middle">{{ $periode->tanggal_akhir }}</td>
              <!-- <td class="align-middle">
                <a href="{{ route('operator/periodes/edit', $periode->id) }}" type="button" class="btn btn-warning">Edit</a>
                <button type="button" class="btn btn-danger" onclick="confirmDelete('{{ route('operator/periodes/delete', $periode->id) }}')">Hapus</button>
              </td> -->
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