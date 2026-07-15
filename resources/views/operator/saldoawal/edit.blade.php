@extends('layouts.applayout')
@section('title', 'Saldo Awal - Edit')
@section('content')

<x-dashboard.page-header
    title="Edit Saldo Awal"
    description="Ubah data saldo awal periode akuntansi"
    :actions="'<a href=\'' . route('operator/saldoawal') . '\' class=\'btn-secondary\'>Kembali</a>'"
/>

<div class="card">
  <div class="card-body">
    <form action="{{ route('operator.saldoawal.update', $saldo_awal->id) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="relative">
          <label for="coa_display" class="label">Pilih COA</label>
          <input type="text" class="input-field @error('coa_id') border-danger @enderror" id="coa_display" placeholder="Pilih COA" value="{{ $saldo_awal->coa->kode_akun }} - {{ $saldo_awal->coa->nama_akun }}" required>
          <input type="hidden" id="coa_id" name="coa_id" value="{{ $saldo_awal->coa->id }}">
          <div id="coa_dropdown" style="display: none;"></div>
          @error('coa_id')
          <p class="text-sm text-danger mt-1">{{ $message }}</p>
          @enderror
        </div>
        <datalist id="coa_list" style="display: none;">
          @foreach ($coas as $coa)
          <option value="{{ $coa->kode_akun }} - {{ $coa->nama_akun }}" data-id="{{ $coa->id }}" data-saldo-normal="{{ $coa->saldo_normal }}"></option>
          @endforeach
        </datalist>

        <div>
          <label for="tanggal_saldo" class="label">Tanggal Saldo</label>
          <input type="date" class="input-field @error('tanggal_saldo') border-danger @enderror" id="tanggal_saldo" name="tanggal_saldo" value="{{ $saldo_awal->tanggal_saldo }}" required>
          @error('tanggal_saldo')
          <p class="text-sm text-danger mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div id="debit-group">
          <label for="debit" class="label">Saldo Awal</label>
          <input type="text" class="input-field @error('debit') border-danger @enderror" id="debit" name="debit" value="{{ number_format($saldo_awal->debit, 2) }}" required>
          @error('debit')
          <p class="text-sm text-danger mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div id="kredit-group" style="display: none;">
          <label for="kredit" class="label">Kredit</label>
          <input type="text" class="input-field @error('kredit') border-danger @enderror" id="kredit" name="kredit" value="{{ number_format($saldo_awal->kredit, 2) }}" disabled>
          @error('kredit')
          <p class="text-sm text-danger mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label for="periode-select" class="label">Periode</label>
          <select class="select-field @error('periode_id') border-danger @enderror" id="periode-select" name="periode_id" required>
            <option value="">Pilih Periode</option>
            @foreach ($periodes as $periode)
            <option value="{{ $periode->id }}" {{ $saldo_awal->periode_id == $periode->id ? 'selected' : '' }} data-start="{{ $periode->tanggal_awal }}" data-end="{{ $periode->tanggal_akhir }}">
              {{ $periode->nama_periode }} ({{ $periode->tanggal_awal }} - {{ $periode->tanggal_akhir }})
            </option>
            @endforeach
          </select>
          @error('periode_id')
          <p class="text-sm text-danger mt-1">{{ $message }}</p>
          @enderror
        </div>
      </div>

      <div class="flex items-center gap-3 mt-6">
        <button type="submit" class="btn-primary">Update</button>
      </div>
    </form>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const coaInput = document.getElementById('coa_display');
    const coaDropdown = document.getElementById('coa_dropdown');
    const coaOptions = document.querySelectorAll('#coa_list option');
    const hiddenInput = document.getElementById('coa_id');

    let activeIndex = -1;

    coaInput.addEventListener('focus', function() {
      const currentValue = coaInput.value.trim().toLowerCase();
      coaDropdown.innerHTML = '';
      activeIndex = -1;
      const matchedOptions = Array.from(coaOptions).filter(opt =>
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
      coaOptions.forEach(option => {
        const div = createDropdownItem(option);
        coaDropdown.appendChild(div);
      });
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
      div.setAttribute('data-id', option.dataset.id);
      div.setAttribute('data-saldo-normal', option.dataset.saldoNormal);
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
      hiddenInput.value = div.getAttribute('data-id');
      coaDropdown.style.display = 'none';
      handleSaldoNormal(div.getAttribute('data-saldo-normal'));
    }

    function handleSaldoNormal(saldoNormal) {
      if (saldoNormal === 'debit') {
        debitField.style.display = 'block';
        debitField.required = true;
        debitField.disabled = false;
        kreditField.style.display = 'none';
        kreditField.value = 0;
        kreditField.required = false;
        kreditField.disabled = true;
      } else {
        kreditField.style.display = 'block';
        kreditField.required = true;
        kreditField.disabled = false;
        debitField.style.display = 'none';
        debitField.value = 0;
        debitField.required = false;
        debitField.disabled = true;
      }
    }

    const debitField = document.getElementById('debit');
    const kreditField = document.getElementById('kredit');

    function formatNumberInput(event) {
      let input = event.target.value.replace(/[^0-9-]/g, '');
      const isNegative = input.startsWith('-');
      input = input.replace(/[^0-9]/g, '');
      if (input.length > 3) {
        input = input.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
      }
      event.target.value = isNegative ? `-${input}` : input;
    }

    function cleanNumberInput(value) {
      return value.replace(/\./g, '');
    }

    document.querySelector('form').addEventListener('submit', function(event) {
      document.getElementById('debit').value = cleanNumberInput(document.getElementById('debit').value);
      document.getElementById('kredit').value = cleanNumberInput(document.getElementById('kredit').value);
    });

    document.getElementById('debit').addEventListener('input', formatNumberInput);
    document.getElementById('kredit').addEventListener('input', formatNumberInput);

    function saveSelectedPeriode() {
      const selectedPeriode = document.getElementById('periode-select').value;
      localStorage.setItem('selectedPeriode', selectedPeriode);
    }

    function setSelectedPeriode() {
      const selectedPeriode = localStorage.getItem('selectedPeriode');
      if (selectedPeriode) {
        document.getElementById('periode-select').value = selectedPeriode;
      }
    }

    document.getElementById('periode-select').addEventListener('change', function() {
      saveSelectedPeriode();
    });

    setSelectedPeriode();
  });
</script>

@endsection
