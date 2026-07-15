@extends('layouts.applayout')
@section('title', 'Buku Besar')
@section('content')

@php
$exportAction = '';
if (isset($entries) && count($entries) > 0 && isset($periodeId) && isset($selectedCoa) && isset($bulan)) {
    $exportUrl = route('rootsuperuser/bukubesar/export', [
        'periode_id' => $periodeId,
        'coa_id' => $selectedCoa->id,
        'bulan' => $bulan,
    ]);
    $exportAction = '<a href="' . $exportUrl . '" class="btn-success btn-sm">Export</a>';
}
@endphp

<x-dashboard.page-header
    title="Buku Besar"
    description="Lihat detail transaksi per akun"
    :actions="$exportAction"
/>

<div class="filter-card mb-6">
  <div class="card-body">
    <div class="filter-row">
      <form action="{{ route('rootsuperuser/bukubesar/searchCoaByPeriod') }}" method="GET" class="contents">
        <div class="filter-group">
          <label for="periode-select" class="label">Pilih Periode</label>
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

      <form action="{{ route('rootsuperuser/bukubesar/showAll') }}" method="GET" class="contents">
        @csrf
        <input type="hidden" name="periode_id" value="{{ $periodeId ?? '' }}">
        <div class="filter-group relative">
          <label for="coa_display" class="label">Pilih COA</label>
          <input class="input-field" data-list="coa_list" id="coa_display" placeholder="Pilih COA" required>
          <datalist id="coa_list">
            @foreach($coas as $coa)
            <option value="{{ $coa->kode_akun }} - {{ $coa->nama_akun }}" data-id="{{ $coa->id }}"></option>
            @endforeach
          </datalist>
          <input type="hidden" id="coa_id" name="coa_id" value="{{ isset($selectedCoa) ? $selectedCoa->id : '' }}">
          <div id="coa_dropdown" class="custom-dropdown"></div>
        </div>
        <div class="filter-group">
          <label for="bulan" class="label">Pilih Bulan</label>
          <select name="bulan" id="bulan" class="select-field">
            <option value="">Pilih Bulan</option>
            @foreach ($availableMonths as $month)
            <option value="{{ $month }}" {{ request('bulan') == $month ? 'selected' : '' }}>
              {{ date('F', mktime(0, 0, 0, $month, 1)) }}
            </option>
            @endforeach
          </select>
        </div>
        <div class="filter-group">
          <label class="label">&nbsp;</label>
          <button type="submit" class="btn-secondary btn-sm">Tampilkan Data</button>
        </div>
      </form>
    </div>
  </div>
</div>

@if(isset($periodeId) && isset($selectedCoa) && isset($bulan))

@if(isset($entries) && count($entries) > 0)

<div class="card mb-6">
  <div class="card-body">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
      <div>
        <span class="block text-gray-500 text-xs uppercase tracking-wide mb-0.5">Periode</span>
        <span class="font-semibold text-gray-900">{{ $periodes->find($periodeId)->nama_periode ?? '-' }}</span>
      </div>
      <div>
        <span class="block text-gray-500 text-xs uppercase tracking-wide mb-0.5">Kode Akun</span>
        <span class="font-semibold text-gray-900">{{ $selectedCoa->kode_akun ?? '-' }}</span>
      </div>
      <div>
        <span class="block text-gray-500 text-xs uppercase tracking-wide mb-0.5">Nama Akun</span>
        <span class="font-semibold text-gray-900">{{ $selectedCoa->nama_akun ?? '-' }}</span>
      </div>
      <div>
        <span class="block text-gray-500 text-xs uppercase tracking-wide mb-0.5">Bulan</span>
        <span class="font-semibold text-gray-900">{{ date('F', mktime(0, 0, 0, $bulan, 1)) }}</span>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-container">
      <table class="data-table">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Nomor Bukti</th>
            <th>Deskripsi</th>
            <th class="num-col">Debit</th>
            <th class="num-col">Kredit</th>
            @if(isset($action) && $action === 'show_all')
            <th class="num-col">Balance</th>
            @endif
          </tr>
        </thead>
        <tbody>
          @php
          $totalDebit = 0;
          $totalCredit = 0;
          $finalRunningTotal = isset($runningTotal) ? $runningTotal : 0;
          @endphp
          @foreach ($entries as $entry)
          @php
          $totalDebit += $entry->debit;
          $totalCredit += $entry->kredit;
          @endphp
          <tr>
            <td>{{ $entry->tanggal_jurnal }}</td>
            <td>{{ $entry->nomor_bukti ?? '-' }}</td>
            <td>{{ $entry->keterangan }}</td>
            <td class="num-col">{{ $entry->debit < 0 ? '(' . number_format(abs($entry->debit), 2) . ')' : number_format($entry->debit, 2) }}</td>
            <td class="num-col">{{ $entry->kredit < 0 ? '(' . number_format(abs($entry->kredit), 2) . ')' : number_format($entry->kredit, 2) }}</td>
            @if(isset($action) && $action === 'show_all')
            <td class="num-col">{{ $entry->running_total < 0 ? '(' . number_format(abs($entry->running_total), 2) . ')' : number_format($entry->running_total, 2) }}</td>
            @endif
          </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <th colspan="3" class="text-right">Total:</th>
            <th class="num-col">{{ number_format($totalDebit, 2) }}</th>
            <th class="num-col">{{ number_format($totalCredit, 2) }}</th>
            @if(isset($action) && $action === 'show_all')
            <th class="num-col">{{ $finalRunningTotal < 0 ? '(' . number_format(abs($finalRunningTotal), 2) . ')' : number_format($finalRunningTotal, 2) }}</th>
            @endif
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>

@else

<x-dashboard.empty-state
    title="Tidak Ada Data"
    description="Tidak ada entri jurnal untuk kriteria yang dipilih."
/>

@endif
@endif

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const coaInput = document.getElementById('coa_display');
    const hiddenCoaInput = document.getElementById('coa_id');
    const coaDropdown = document.getElementById('coa_dropdown');
    const options = document.getElementById('coa_list').options;

    let activeIndex = -1;

    coaInput.addEventListener('focus', function() {
      const currentValue = coaInput.value.trim().toLowerCase();
      coaDropdown.innerHTML = '';
      activeIndex = -1;
      const matchedOptions = Array.from(options).filter(opt =>
        opt.value.trim().toLowerCase() === currentValue
      );
      if (matchedOptions.length === 1) {
        const option = matchedOptions[0];
        const div = createDropdownItem(option);
        coaDropdown.appendChild(div);
      } else {
        showDropdown();
        filterDropdown();
      }
      coaDropdown.style.display = 'block';
    });

    coaInput.addEventListener('input', function() {
      filterDropdown();
      activeIndex = -1;
      const visible = getVisibleItems();
      if (visible.length) highlightItem(visible, 0);
    });

    coaInput.addEventListener('keydown', function(e) {
      const visible = getVisibleItems();
      if (visible.length === 0) return;
      if (e.key === 'ArrowDown') {
        e.preventDefault();
        activeIndex = (activeIndex + 1) % visible.length;
        highlightItem(visible, activeIndex);
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        activeIndex = (activeIndex - 1 + visible.length) % visible.length;
        highlightItem(visible, activeIndex);
      } else if (e.key === 'Enter') {
        e.preventDefault();
        if (visible.length === 1) {
          selectItem(visible[0]);
        } else {
          const exactMatch = visible.find(item =>
            item.textContent.trim().toLowerCase() === coaInput.value.trim().toLowerCase()
          );
          if (exactMatch) {
            selectItem(exactMatch);
          } else if (activeIndex >= 0 && visible[activeIndex]) {
            selectItem(visible[activeIndex]);
          }
        }
      }
    });

    document.addEventListener('click', function(event) {
      if (!coaInput.contains(event.target) && !coaDropdown.contains(event.target)) {
        coaDropdown.style.display = 'none';
      }
    });

    function showDropdown() {
      coaDropdown.innerHTML = '';
      for (let i = 0; i < options.length; i++) {
        const option = options[i];
        const div = createDropdownItem(option);
        coaDropdown.appendChild(div);
      }
      coaDropdown.style.position = 'absolute';
      coaDropdown.style.backgroundColor = 'white';
      coaDropdown.style.border = '1px solid #ccc';
      coaDropdown.style.maxHeight = '200px';
      coaDropdown.style.overflowY = 'auto';
      coaDropdown.style.zIndex = '1000';
      coaDropdown.style.width = `${coaInput.offsetWidth}px`;
      coaDropdown.style.display = 'block';
    }

    function createDropdownItem(option) {
      const div = document.createElement('div');
      div.textContent = option.value;
      div.setAttribute('data-id', option.getAttribute('data-id'));
      div.classList.add('dropdown-item');
      div.style.padding = '8px';
      div.style.cursor = 'pointer';
      div.style.backgroundColor = 'white';
      div.addEventListener('click', function() {
        selectItem(div);
      });
      return div;
    }

    function filterDropdown() {
      const filter = coaInput.value.toLowerCase();
      const divs = coaDropdown.getElementsByClassName('dropdown-item');
      for (let i = 0; i < divs.length; i++) {
        const div = divs[i];
        div.style.display = div.textContent.toLowerCase().includes(filter) ? '' : 'none';
      }
    }

    function getVisibleItems() {
      return Array.from(coaDropdown.querySelectorAll('.dropdown-item'))
        .filter(item => item.style.display !== 'none');
    }

    function highlightItem(items, index) {
      items.forEach((item, i) => {
        item.style.backgroundColor = i === index ? '#e0e0e0' : 'white';
      });
      items[index].scrollIntoView({ block: 'nearest' });
    }

    function selectItem(div) {
      coaInput.value = div.textContent;
      hiddenCoaInput.value = div.getAttribute('data-id');
      coaDropdown.style.display = 'none';
    }

  });
  });
</script>

@endsection
