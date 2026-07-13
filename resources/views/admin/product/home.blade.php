@extends('layouts.applayout')
@section('content')
@include('components.admin-sidebar', ['activeMenu' => 'products'])

<!-- Content wrapper -->
<div class="content-wrapper">
  <!-- Content -->
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-0">List User</h5>
        <a href="{{ route('admin/products/create') }}" class="btn btn-primary">Tambah User</a>
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
              <th>Foto Profil</th>
              <th>Nama</th>
              <th>Email</th>
              <th>Usertype</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($users as $user)
            <tr>
              <td class="align-middle">{{ $loop->iteration }}</td>
              <td class="align-middle">
                @if($user->image)
                <img src="{{ asset('storage/' . $user->image) }}" alt="{{ $user->name }}" width="50" height="50" class="rounded-circle">
                @else
                <span>No Image</span>
                @endif
              </td>
              <td class="align-middle">{{ $user->name }}</td>
              <td class="align-middle">{{ $user->email }}</td>
              <td class="align-middle">{{ $user->usertype }}</td>
              <td class="align-middle">
                @if(!in_array($user->usertype, ['rootsuperuser', 'admin']))
                <a href="{{ route('admin/products/status', $user->id) }}" class="btn btn-{{ $user->status ? 'danger' : 'success' }}">
                  {{ $user->status ? 'Deactivate' : 'Activate' }}
                </a>
                <a href="{{ route('admin/products/edit', $user->id) }}" type="button" class="btn btn-warning">Edit</a>
                <button type="button" class="btn btn-danger" onclick="confirmDelete('{{ route('admin/products/delete', $user->id) }}')">Hapus</button>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td class="text-center" colspan="6">No users found</td>
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

<div class="toast-container position-fixed top-50 start-50 translate-middle p-3" style="z-index: 1050;">
  <div id="deleteToast" class="toast bg-warning text-white" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <i class="bx bx-bell me-2"></i>
      <strong class="me-auto">Konfirmasi Hapus</strong>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
      Apakah Anda yakin ingin menghapus user ini?
      <div class="d-flex justify-content-end mt-4 pt-2 border-top">
        <button type="button" class="btn btn-light btn-sm me-2" data-bs-dismiss="toast">Batal</button>
        <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteBtn">Hapus</button>
      </div>
    </div>
  </div>
</div>

<script>
  function confirmDelete(deleteUrl) {
    var toastEl = document.getElementById('deleteToast');
    var toast = new bootstrap.Toast(toastEl);
    toast.show();

    document.getElementById('confirmDeleteBtn').onclick = function() {
      window.location.href = deleteUrl;
    };
  }
</script>


@endsection