@extends('layouts.applayout')
@section('title', 'Saldo Awal')
@section('content')

<x-dashboard.page-header
    title="Saldo Awal"
    description="Kelola saldo awal periode akuntansi"
    :actions="'<a href=\'' . route('rootsuperuser/saldoawal/create') . '\' class=\'btn-primary\'>Tambah Saldo Awal</a>'"
/>

<div class="filter-card mb-6">
  <div class="card-body">
    <form method="GET" action="{{ route('rootsuperuser/saldoawal') }}">
      <div class="filter-row">
        <div class="filter-group">
          <label for="periode" class="label">Pilih Periode</label>
          <select name="periode_id" id="periode" class="select-field">
            <option value="">Pilih Periode</option>
            @foreach ($periodes as $periode)
            <option value="{{ $periode->id }}" {{ request('periode_id') == $periode->id ? 'selected' : '' }}>
              {{ $periode->nama_periode }}
            </option>
            @endforeach
          </select>
        </div>
        <div class="filter-group">
          <label for="bulan" class="label">Pilih Bulan</label>
          <select name="bulan" id="bulan" class="select-field">
            <option value="">Pilih Bulan</option>
            @for ($i = 1; $i <= 12; $i++)
              <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
              {{ date('F', mktime(0, 0, 0, $i, 1)) }}
              </option>
              @endfor
          </select>
        </div>
        <div class="flex items-end">
          <button type="submit" class="btn-primary">Tampil</button>
        </div>
      </div>
    </form>
  </div>
</div>

@if(request()->filled('periode_id') && request()->filled('bulan'))
<div class="card">
  <div class="card-header border-b border-gray-100 px-6 py-4">
    <div class="relative w-full max-w-sm">
      <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
      <input type="text" id="search-field" class="input-field pl-10" placeholder="Cari Saldo Awal">
    </div>
  </div>
  <div class="card-body p-6">
    @if($saldo_awals->isEmpty())
    <x-dashboard.empty-state
        icon="wallet"
        title="Belum ada data saldo awal"
        description="Belum ada saldo awal untuk periode dan bulan yang dipilih"
        :action="'<a href=\'' . route('rootsuperuser/saldoawal/create') . '\' class=\'btn-primary btn-sm\'>Tambah Saldo Awal</a>'"
    />
    @else
    <div class="table-container overflow-x-auto">
      <table class="data-table w-full">
        <thead>
          <tr>
            <th class="sticky top-0 bg-gray-50 z-10">Kode COA</th>
            <th class="sticky top-0 bg-gray-50 z-10">Tanggal Saldo</th>
            <th class="sticky top-0 bg-gray-50 z-10">COA</th>
            <th class="sticky top-0 bg-gray-50 z-10 num-col">Saldo Awal</th>
            <th class="sticky top-0 bg-gray-50 z-10">Periode</th>
            <th class="sticky top-0 bg-gray-50 z-10">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($saldo_awals->sortBy('coa.kode_akun') as $saldo_awal)
          <tr class="hover:bg-gray-50/50 transition-colors">
            <td class="font-mono text-sm">{{ $saldo_awal->coa->kode_akun }}</td>
            <td>{{ $saldo_awal->tanggal_saldo }}</td>
            <td>{{ $saldo_awal->coa->nama_akun }}</td>
            <td class="num-col">
              @if($saldo_awal->debit < 0)
                ({{ number_format(abs($saldo_awal->debit), 2) }})
                @else
                {{ number_format($saldo_awal->debit, 2) }}
                @endif
            </td>
            <td>{{ $saldo_awal->periode->nama_periode }}</td>
            <td>
              <div class="flex items-center gap-2">
                <a href="{{ route('rootsuperuser.saldoawal.edit', $saldo_awal->id) }}" class="btn-warning btn-sm">Edit</a>
                <button type="button" class="btn-danger btn-sm" onclick="window.dispatchEvent(new CustomEvent('delete-modal-open', {detail: '{{ route('rootsuperuser.saldoawal.destroy', $saldo_awal->id) }}'}))">Hapus</button>
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
@elseif(!request()->filled('periode_id') && !request()->filled('bulan'))
<div class="card">
  <div class="card-body">
    <x-dashboard.empty-state
        icon="filter"
        title="Pilih filter untuk mulai"
        description="Silakan pilih periode dan bulan terlebih dahulu untuk menampilkan data saldo awal"
    />
  </div>
</div>
@endif

<x-delete-modal
    title="Konfirmasi Hapus Saldo Awal"
    message="Apakah Anda yakin ingin menghapus Saldo Awal ini? Data yang sudah dihapus tidak dapat dikembalikan."
/>

<script>
  document.getElementById('search-field')?.addEventListener('keyup', function() {
    let input = this.value.toLowerCase();
    let table = document.querySelector("table tbody");
    if (!table) return;
    let rows = table.getElementsByTagName("tr");
    for (let i = 0; i < rows.length; i++) {
      let coaCode = rows[i].getElementsByTagName("td")[0];
      let coaName = rows[i].getElementsByTagName("td")[2];
      if (coaCode && coaName) {
        let codeText = coaCode.textContent || coaCode.innerText;
        let nameText = coaName.textContent || coaName.innerText;
        rows[i].style.display = (codeText.toLowerCase().includes(input) || nameText.toLowerCase().includes(input)) ? "" : "none";
      }
    }
  });
</script>

@endsection
