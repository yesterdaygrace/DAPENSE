@extends('layouts.applayout')
@section('title', 'User Management')
@section('content')

<x-dashboard.page-header
  title="User Management"
  description="Kelola pengguna sistem"
  :actions="'<a href=\"' . route('rootsuperuser/products/create') . '\" class=\"btn-primary\"><i data-lucide=\"plus\" class=\"w-4 h-4\"></i> Tambah User</a>'"
/>

<div class="card">
  <div class="card-body">
    <div class="overflow-x-auto">
      <table class="data-table">
        <thead>
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
            <td>{{ $loop->iteration }}</td>
            <td>
              @if($user->image)
              <img src="{{ asset('storage/' . $user->image) }}" alt="{{ $user->name }}" width="50" height="50" class="rounded-full object-cover">
              @else
              <div class="w-[50px] h-[50px] rounded-full bg-gray-100 flex items-center justify-center">
                <i data-lucide="user" class="w-5 h-5 text-gray-400"></i>
              </div>
              @endif
            </td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td><span class="badge badge-primary">{{ $user->usertype }}</span></td>
            <td>
              @if(!in_array($user->usertype, ['rootsuperuser', 'admin']))
              <div class="flex items-center gap-2">
                <a href="{{ route('rootsuperuser/products/status', $user->id) }}" class="btn-{{ $user->status ? 'danger' : 'success' }} btn-sm">
                  {{ $user->status ? 'Deactivate' : 'Activate' }}
                </a>
                <a href="{{ route('rootsuperuser/products/edit', $user->id) }}" class="btn-warning btn-sm">Edit</a>
                <button type="button" class="btn-danger btn-sm" onclick="window.dispatchEvent(new CustomEvent('delete-modal-open', {detail: '{{ route('rootsuperuser/products/delete', $user->id) }}'}))">Hapus</button>
              </div>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6">
              <x-dashboard.empty-state
                icon="users"
                title="Belum ada user"
                description="Mulai dengan menambahkan user baru ke sistem."
                :action="'<a href=\"' . route('rootsuperuser/products/create') . '\" class=\"btn-primary btn-sm\">Tambah User</a>'"
              />
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<x-delete-modal title="Hapus User" message="Apakah Anda yakin ingin menghapus user ini? Data yang dihapus tidak dapat dikembalikan." />

@endsection
