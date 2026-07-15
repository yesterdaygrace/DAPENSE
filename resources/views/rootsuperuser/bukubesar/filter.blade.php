@extends('layouts.applayout')
@section('title', 'Buku Besar - Filter')
@section('content')

<x-dashboard.page-header
    title="Buku Besar"
    description="Lihat detail transaksi per akun per tanggal"
    :actions="'<a href=\'' . route('rootsuperuser/bukubesar') . '\' class=\'btn-secondary btn-sm\'>Go Back</a>'"
/>

<div class="filter-card mb-6">
  <div class="card-body">
    <div class="filter-row">
      <form action="{{ route('rootsuperuser/bukubesar/searchCoaByFilter') }}" method="GET" class="contents">
        <div class="filter-group">
          <label for="periode-select" class="label">Select Periode</label>
          <div class="flex gap-2 items-end">
            <select class="select-field" id="periode-select" name="periode_id" required>
              <option value="">Pilih Periode</option>
              @if(isset($periodes) && $periodes->count())
              @foreach($periodes as $periode)
              <option value="{{ $periode->id }}" {{ isset($periodeId) && $periode->id == $periodeId ? 'selected' : '' }}>
                {{ $periode->nama_periode }}
              </option>
              @endforeach
              @else
              <option value="" disabled>No periods available</option>
              @endif
            </select>
            <button type="submit" class="btn-primary btn-sm">Pilih</button>
          </div>
        </div>
      </form>

      <form action="{{ route('rootsuperuser/bukubesar/searchByDate') }}" method="GET" class="contents">
        @csrf
        <input type="hidden" name="periode_id" value="{{ $periodeId ?? '' }}">
        <div class="filter-group relative">
          <label for="coa_display" class="label">Pilih COA</label>
          <input type="text" class="input-field" id="coa_display" list="coa_list" placeholder="Pilih COA" required>
          <datalist id="coa_list">
            @foreach($coas as $coa)
            <option value="{{ $coa->kode_akun }} - {{ $coa->nama_akun }}" data-id="{{ $coa->id }}"></option>
            @endforeach
          </datalist>
          <input type="hidden" id="coa_id" name="coa_id" value="{{ isset($selectedCoa) ? $selectedCoa->id : '' }}">
          <div id="coa_dropdown" class="custom-dropdown"></div>
        </div>
        <div class="filter-group">
          <label for="tanggal_awal" class="label">Start Date</label>
          <input type="date" class="input-field" id="tanggal_awal" name="tanggal_awal" required>
        </div>
        <div class="filter-group">
          <label for="tanggal_akhir" class="label">End Date</label>
          <input type="date" class="input-field" id="tanggal_akhir" name="tanggal_akhir" required>
        </div>
        <div class="filter-group">
          <label class="label">&nbsp;</label>
          <button type="submit" class="btn-secondary btn-sm">Filter Data</button>
        </div>
      </form>
    </div>
  </div>
</div>

@if(isset($periodeId) && isset($selectedCoa) && isset($tanggalAwal) && isset($tanggalAkhir))

<div class="card mb-6">
  <div class="card-body">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
      <div>
        <span class="block text-gray-500 text-xs uppercase tracking-wide mb-0.5">Periode</span>
        <span class="font-semibold text-gray-900">{{ $periodes->find($periodeId)->nama_periode ?? '-' }} ({{ $tanggalAwal }} - {{ $tanggalAkhir }})</span>
      </div>
      <div>
        <span class="block text-gray-500 text-xs uppercase tracking-wide mb-0.5">Kode Akun</span>
        <span class="font-semibold text-gray-900">{{ $selectedCoa->kode_akun ?? '-' }}</span>
      </div>
      <div>
        <span class="block text-gray-500 text-xs uppercase tracking-wide mb-0.5">Nama Akun</span>
        <span class="font-semibold text-gray-900">{{ $selectedCoa->nama_akun ?? '-' }}</span>
      </div>
    </div>
  </div>
</div>

@if(isset($entries) && count($entries) > 0)

<div class="card">
  <div class="card-body p-0">
    <div class="table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Nomor Bukti</th>
            <th>Description</th>
            <th class="num-col">Debit</th>
            <th class="num-col">Credit</th>
            <th class="num-col">Total Balance</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>{{ $tanggalAwal }}</td>
            <td></td>
            <td></td>
            <td class="num-col">{{ number_format($saldoAwal > 0 ? $saldoAwal : 0, 2) }}</td>
            <td class="num-col">{{ number_format($saldoAwal < 0 ? abs($saldoAwal) : 0, 2) }}</td>
            <td class="num-col">{{ number_format($saldoAwal, 2) }}</td>
          </tr>
          @php $runningTotal = $saldoAwal; @endphp
          @foreach ($entries as $entry)
          @php
          $runningTotal += ($entry->debit - $entry->kredit);
          @endphp
          <tr>
            <td>{{ $entry->tanggal_jurnal }}</td>
            <td>{{ $entry->nomor_bukti ?? '-' }}</td>
            <td>{{ $entry->keterangan }}</td>
            <td class="num-col">{{ number_format($entry->debit, 2) }}</td>
            <td class="num-col">{{ number_format($entry->kredit, 2) }}</td>
            <td class="num-col">{{ number_format($runningTotal, 2) }}</td>
          </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <th colspan="5" class="text-right">Total Balance:</th>
            <th class="num-col">{{ number_format($runningTotal, 2) }}</th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

@else

<x-dashboard.empty-state
    title="Tidak Ada Data"
    description="Tidak ada entri jurnal untuk rentang tanggal yang dipilih."
/>

@endif
@endif

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const coaInput = document.getElementById('coa_display');
    const hiddenCoaInput = document.getElementById('coa_id');
    const coaDropdown = document.getElementById('coa_dropdown');
    const options = document.getElementById('coa_list').options;

    coaInput.addEventListener('focus', showDropdown);
    coaInput.addEventListener('input', filterDropdown);
    document.addEventListener('click', function(event) {
      if (!coaInput.contains(event.target) && !coaDropdown.contains(event.target)) {
        coaDropdown.style.display = 'none';
      }
    });

    function showDropdown() {
      coaDropdown.innerHTML = '';
      for (let i = 0; i < options.length; i++) {
        const option = options[i];
        const div = document.createElement('div');
        div.textContent = option.value;
        div.setAttribute('data-id', option.getAttribute('data-id'));
        div.style.padding = '8px';
        div.style.cursor = 'pointer';
        div.addEventListener('click', function() {
          coaInput.value = option.value;
          hiddenCoaInput.value = option.getAttribute('data-id');
          coaDropdown.style.display = 'none';
        });
        div.addEventListener('mouseover', function() {
          div.style.backgroundColor = '#f1f1f1';
        });
        div.addEventListener('mouseout', function() {
          div.style.backgroundColor = 'white';
        });
        coaDropdown.appendChild(div);
      }
      coaDropdown.style.position = 'absolute';
      coaDropdown.style.backgroundColor = 'white';
      coaDropdown.style.border = '1px solid #ccc';
      coaDropdown.style.maxHeight = '200px';
      coaDropdown.style.overflowY = 'auto';
      coaDropdown.style.zIndex = '1000';
      coaDropdown.style.display = 'block';
    }

    function filterDropdown() {
      const filter = coaInput.value.toLowerCase();
      const divs = coaDropdown.getElementsByTagName('div');
      for (let i = 0; i < divs.length; i++) {
        const div = divs[i];
        if (div.textContent.toLowerCase().indexOf(filter) > -1) {
          div.style.display = '';
        } else {
          div.style.display = 'none';
        }
      }
    }

    const rows = document.querySelectorAll('table tbody tr');
    rows.forEach((row) => {
      const debit = parseFloat(row.querySelector('td:nth-child(4)').textContent.replace(/,/g, '') || 0);
      const kredit = parseFloat(row.querySelector('td:nth-child(5)').textContent.replace(/,/g, '') || 0);
      const tanggal = row.querySelector('td:first-child').textContent.trim();
      const nomorBukti = row.querySelector('td:nth-child(2)').textContent.trim();
      const keteranganCell = row.querySelector('td:nth-child(3)');
      if (keteranganCell.textContent.includes('Saldo Awal')) return;
      if (debit === 0 && kredit === 0) {
        keteranganCell.textContent = tanggal;
        return;
      }
      if (nomorBukti.startsWith('KK') && kredit > 0) {
        keteranganCell.textContent = `PENGELUARAN KAS ${tanggal}`;
      } else if (nomorBukti.startsWith('KK') && debit > 0) {
        keteranganCell.textContent = `PEMASUKAN KAS ${tanggal}`;
      } else if (nomorBukti.startsWith('KM') && kredit > 0) {
        keteranganCell.textContent = `PENGELUARAN KAS ${tanggal}`;
      } else if (nomorBukti.startsWith('KM') && debit > 0) {
        keteranganCell.textContent = `PEMASUKAN KAS ${tanggal}`;
      } else if (nomorBukti.includes('-BK-') && kredit > 0) {
        keteranganCell.textContent = `PENGELUARAN BANK ${tanggal}`;
      } else if (nomorBukti.includes('-BK-') && debit > 0) {
        keteranganCell.textContent = `PEMASUKAN BANK ${tanggal}`;
      } else if (nomorBukti.includes('-BM-') && kredit > 0) {
        keteranganCell.textContent = `PENGELUARAN BANK ${tanggal}`;
      } else if (nomorBukti.includes('-BM-') && debit > 0) {
        keteranganCell.textContent = `PEMASUKAN BANK ${tanggal}`;
      } else if (kredit > 0) {
        keteranganCell.textContent = `PENGELUARAN ${tanggal}`;
      } else if (debit > 0) {
        keteranganCell.textContent = `PEMASUKAN ${tanggal}`;
      }
    });
  });
</script>

@endsection
