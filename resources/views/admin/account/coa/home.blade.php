@extends('layouts.applayout')
@section('title', 'COA')
@section('content')

<x-dashboard.page-header
    title="Chart of Accounts"
    description="Kelola data akun perusahaan"
    :actions="'<a href=\'' . route('admin/account/coa/create') . '\' class=\'btn-primary\'>Tambah COA</a>'"
/>

<div class="card rounded-card border border-gray-100 shadow-card">
  <div class="card-header border-b border-gray-100 px-6 py-4">
    <div class="relative w-full max-w-sm">
      <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
      <input type="text" id="search-field" class="input-field pl-10" placeholder="Cari Kode COA atau Nama COA">
    </div>
  </div>

  <div class="card-body p-6">
    @if($coas->isEmpty())
    <x-dashboard.empty-state
        icon="folder-open"
        title="Belum ada data COA"
        description="Mulai tambahkan akun untuk perusahaan Anda"
        :action="'<a href=\'' . route('admin/account/coa/create') . '\' class=\'btn-primary btn-sm\'>Tambah COA</a>'"
    />
    @else
    <div class="table-container overflow-x-auto">
      <table class="data-table w-full" id="coa-table">
        <thead>
          <tr>
            <th class="sticky top-0 bg-gray-50 z-10">Kode Akun</th>
            <th class="sticky top-0 bg-gray-50 z-10">Nama Akun</th>
            <th class="sticky top-0 bg-gray-50 z-10">Saldo Normal</th>
            <th class="sticky top-0 bg-gray-50 z-10">Kategori</th>
            <th class="sticky top-0 bg-gray-50 z-10">Level</th>
            <th class="sticky top-0 bg-gray-50 z-10">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($coas as $coa)
          <tr class="hover:bg-gray-50/50 transition-colors">
            <td class="font-mono text-sm">{{ $coa->kode_akun }}</td>
            <td class="uppercase">{{ $coa->nama_akun }}</td>
            <td><span class="badge badge-info">{{ $coa->saldo_normal }}</span></td>
            <td class="uppercase">{{ $coa->kategori }}</td>
            <td>{{ $coa->level }}</td>
            <td>
              <div class="flex items-center gap-2">
                <a href="{{ route('admin/account/coa/edit', $coa->id) }}" class="btn-warning btn-sm">Edit</a>
                <button type="button" class="btn-danger btn-sm" onclick="window.dispatchEvent(new CustomEvent('delete-modal-open', {detail: '{{ route('admin/account/coa/delete', $coa->id) }}'}))">Hapus</button>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </div>
</div>

<x-delete-modal
    title="Konfirmasi Hapus COA"
    message="Apakah Anda yakin ingin menghapus COA ini? Data yang sudah dihapus tidak dapat dikembalikan."
/>

<script>
  document.getElementById('search-field').addEventListener('keyup', function() {
    let searchQuery = this.value.toLowerCase();
    let tableRows = document.querySelectorAll('#coa-table tbody tr');
    tableRows.forEach(row => {
      let kodeAkun = row.cells[0].textContent.toLowerCase();
      let namaAkun = row.cells[1].textContent.toLowerCase();
      row.style.display = (kodeAkun.includes(searchQuery) || namaAkun.includes(searchQuery)) ? '' : 'none';
    });
  });
</script>

@endsection
