@extends('layouts.applayout')
@section('title', 'Header')
@section('content')

<x-dashboard.page-header
    title="Header COA"
    description="Kelola header Chart of Accounts"
    :actions="'<a href=\'' . route('operator/account/header/create') . '\' class=\'btn-primary\'>Tambah Header COA</a>'"
/>

<div class="card rounded-card border border-gray-100 shadow-card">
  <div class="card-header border-b border-gray-100 px-6 py-4">
    <div class="relative w-full max-w-sm">
      <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
      <input type="text" id="search-field" class="input-field pl-10" placeholder="Cari Kode Header atau Nama Header COA">
    </div>
  </div>

  <div class="card-body p-6">
    @if($headerCoas->isEmpty())
    <x-dashboard.empty-state
        icon="folder-open"
        title="Belum ada data Header COA"
        description="Mulai tambahkan header untuk mengorganisasi akun"
        :action="'<a href=\'' . route('operator/account/header/create') . '\' class=\'btn-primary btn-sm\'>Tambah Header</a>'"
    />
    @else
    <div class="table-container overflow-x-auto">
      <table class="data-table w-full" id="header-coa-table">
        <thead>
          <tr>
            <th class="sticky top-0 bg-gray-50 z-10">Kode Header</th>
            <th class="sticky top-0 bg-gray-50 z-10">Nama Header</th>
            <th class="sticky top-0 bg-gray-50 z-10">Level</th>
            <th class="sticky top-0 bg-gray-50 z-10">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($headerCoas as $headerCoa)
          <tr class="hover:bg-gray-50/50 transition-colors">
            <td class="font-mono text-sm">{{ $headerCoa->kode_header }}</td>
            <td class="uppercase">{{ $headerCoa->nama_header }}</td>
            <td><span class="badge badge-info">{{ $headerCoa->level }}</span></td>
            <td>
              <div class="flex items-center gap-2">
                <a href="{{ route('operator/account/header/edit', $headerCoa->id) }}" class="btn-warning btn-sm">Edit</a>
                <button type="button" class="btn-danger btn-sm" onclick="window.dispatchEvent(new CustomEvent('delete-modal-open', {detail: '{{ route('operator/account/header/delete', $headerCoa->id) }}'}))">Hapus</button>
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
    title="Konfirmasi Hapus Header COA"
    message="Apakah Anda yakin ingin menghapus header COA ini? Data yang sudah dihapus tidak dapat dikembalikan."
/>

<script>
  document.getElementById('search-field').addEventListener('keyup', function() {
    let searchQuery = this.value.toLowerCase();
    let tableRows = document.querySelectorAll('#header-coa-table tbody tr');
    tableRows.forEach(row => {
      let kodeHeader = row.cells[0].textContent.toLowerCase();
      let namaHeader = row.cells[1].textContent.toLowerCase();
      row.style.display = (kodeHeader.includes(searchQuery) || namaHeader.includes(searchQuery)) ? '' : 'none';
    });
  });
</script>

@endsection
