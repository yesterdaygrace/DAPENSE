@extends('layouts.applayout')
@section('title', 'Pengaturan')
@section('content')

<x-dashboard.page-header
  title="Otorisator"
  description="Kelola otorisator transaksi"
  :actions="'<a href=\"' . route('rootsuperuser/otorisator/create') . '\" class=\"btn-primary\"><i data-lucide=\"plus\" class=\"w-4 h-4\"></i> Tambah Otorisator</a>'"
/>

<div class="card">
  <div class="card-body">
    <div class="overflow-x-auto">
      <table class="data-table">
        <thead>
          <tr>
            <th>Nama Otorisator</th>
            <th>Jabatan Otorisator</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($otorisators as $otorisator)
          <tr>
            <td>{{ $otorisator->nama_otorisator }}</td>
            <td>{{ $otorisator->jabatan_otorisator }}</td>
            <td>
              <div class="flex items-center gap-2">
                <a href="{{ route('rootsuperuser/otorisator/edit', $otorisator->id) }}" class="btn-warning btn-sm">Edit</a>
                <button type="button" class="btn-danger btn-sm" onclick="window.dispatchEvent(new CustomEvent('delete-modal-open', {detail: '{{ route('rootsuperuser/otorisator/delete', $otorisator->id) }}'}))">Hapus</button>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="3">
              <x-dashboard.empty-state
                icon="users"
                title="Belum ada otorisator"
                description="Mulai dengan menambahkan otorisator baru."
                :action="'<a href=\"' . route('rootsuperuser/otorisator/create') . '\" class=\"btn-primary btn-sm\">Tambah Otorisator</a>'"
              />
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

<x-delete-modal title="Hapus Otorisator" message="Apakah Anda yakin ingin menghapus otorisator ini? Data yang dihapus tidak dapat dikembalikan." />

@endsection
