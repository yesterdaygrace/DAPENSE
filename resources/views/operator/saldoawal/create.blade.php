@extends('layouts.applayout')
@section('content')
<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('operator/dashboard') }}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder ms-2">{{ Auth::user()->name }}</span>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="py-1 menu-inner">
        <!-- Dashboard -->
        <li class="menu-item">
            <a href="{{ route('operator/dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('operator/periodes') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calendar"></i>
                <div data-i18n="Analytics">Periode</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="Layouts">Accounts</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/account/header') }}" class="menu-link">
                        <div data-i18n="Without menu">Header</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/account/coa') }}" class="menu-link">
                        <div data-i18n="Without menu">COA</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/account/headercoa') }}" class="menu-link">
                        <div data-i18n="Without menu">Combine Header & COA</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item active">
            <a href="{{ route('operator/saldoawal') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-money"></i>
                <div data-i18n="Analytics">Saldo Awal</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-notepad"></i>
                <div data-i18n="Layouts">Jurnaling</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling') }}" class="menu-link">
                        <div data-i18n="Without menu">Kas Masuk</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling/kaskeluar') }}" class="menu-link">
                        <div data-i18n="Without menu">Kas Keluar</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling/bankmasuk') }}" class="menu-link">
                        <div data-i18n="Without menu">Bank Masuk</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling/bankkeluar') }}" class="menu-link">
                        <div data-i18n="Without menu">Bank Keluar</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling/memorial') }}" class="menu-link">
                        <div data-i18n="Without menu">Memorial</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling/memorialpenutup') }}" class="menu-link">
                        <div data-i18n="Without menu">Memorial (Penutup)</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling/showing') }}" class="menu-link">
                        <div data-i18n="Without menu">Tampil</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="{{ route('operator/bukubesar') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-book"></i>
                <div data-i18n="Analytics">Buku Besar</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('operator/neracasaldo/') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calculator"></i>
                <div data-i18n="Analytics">Neraca Saldo</div>
            </a>
        </li>
    </ul>
</aside>

@section('content')
<div class="container mt-5">
    <h2>Tambah Saldo Awal</h2>
    @if (session()->has('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif
    <form action="{{ route('operator/saldoawal/store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="coa_display" class="form-label">Pilih COA</label>
            <input type="text" class="form-control" id="coa_display" placeholder="Pilih COA" required>
            <input type="hidden" id="coa_id" name="coa_id">
            <div id="coa_dropdown" style="display: none;"></div>
        </div>
        <datalist id="coa_list" style="display: none;">
            @foreach ($coas as $coa)
            <option value="{{ $coa->kode_akun }} - {{ $coa->nama_akun }}" data-id="{{ $coa->id }}" data-saldo-normal="{{ $coa->saldo_normal }}"></option>
            @endforeach
        </datalist>

        <!-- Tanggal Saldo -->
        <div class="mb-3">
            <label for="tanggal_saldo" class="form-label">Tanggal Saldo</label>
            <input type="date" class="form-control" id="tanggal_saldo" name="tanggal_saldo" required>
        </div>

        <!-- Debit Field -->
        <div class="mb-3" id="debit-group">
            <label for="debit" class="form-label">Saldo Awal</label>
            <input type="text" class="form-control" id="debit" name="debit" disabled>
        </div>

        <!-- Kredit Field -->
        <div class="mb-3" id="kredit-group">
            <label for="kredit" class="form-label">Kredit</label>
            <input type="text" class="form-control" id="kredit" name="kredit" disabled>
        </div>

        <!-- Periode -->
        <div class="mb-3">
            <label for="periode-select" class="form-label">Periode</label>
            <select class="form-control" id="periode-select" name="periode_id" required>
                <option value="">Pilih Periode</option>
                @foreach ($periodes as $periode)
                <option value="{{ $periode->id }}" data-start="{{ $periode->tanggal_awal }}" data-end="{{ $periode->tanggal_akhir }}">
                    {{ $periode->nama_periode }} ({{ $periode->tanggal_awal }} - {{ $periode->tanggal_akhir }})
                </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
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

            // Cari apakah ada yang cocok persis
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
            items[index].scrollIntoView({
                block: 'nearest'
            });
        }

        function selectItem(div) {
            coaInput.value = div.textContent;
            hiddenInput.value = div.getAttribute('data-id');
            coaDropdown.style.display = 'none';
            handleSaldoNormal(div.getAttribute('data-saldo-normal'));
        }

        function handleSaldoNormal(saldoNormal) {
            const debitField = document.getElementById('debit');
            const kreditField = document.getElementById('kredit');

            // Tampilkan hanya input Debit
            debitField.parentElement.style.display = 'block';
            debitField.required = true;
            debitField.disabled = false;

            // Sembunyikan input Kredit
            kreditField.parentElement.style.display = 'none';
            kreditField.value = 0; // Tetapkan nilai default
            kreditField.required = false;
            kreditField.disabled = true;
        }


        function validateNumberInput(event) {
            const regex = /^-?[0-9]*$/;
            if (!regex.test(event.target.value)) {
                event.target.value = event.target.value.replace(/[^0-9-]/g, '');
            }
        }

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