@extends('layouts.applayout')
@section('content')
@include('components.admin-sidebar', ['activeMenu' => 'bukubesar'])


<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header">
                <h2>Buku Besar</h2>
                <!-- <a href="{{ route('admin/bukubesar/filter') }}" class="mb-3 btn btn-primary">Go to Filter Data</a> -->

                <!-- Form for selecting period -->
                <form action="{{ route('admin/bukubesar/searchCoaByPeriod') }}" method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="periode-select" class="form-label">Pilih Periode</label>
                            <select class="form-control" id="periode-select" name="periode_id" required>
                                <option value="">Pilih Periode</option>
                                @if(isset($periodes) && $periodes->count())
                                @foreach($periodes as $periode)
                                <option value="{{ $periode->id }}" {{ isset($periodeId) && $periode->id == $periodeId ?
                'selected' : '' }}>
                                    {{ $periode->nama_periode }}
                                </option>
                                @endforeach
                                @else
                                <option value="" disabled>No periods available</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-4 align-self-end">
                            <button type="submit" class="mt-3 btn btn-primary">Pilih Periode</button>
                        </div>
                    </div>
                </form>

                <form action="{{ route('admin/bukubesar/showAll') }}" method="GET">
                    @csrf
                    <input type="hidden" name="periode_id" value="{{ $periodeId ?? '' }}">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="coa_display" class="form-label">Pilih COA</label>
                            <input class="form-control" data-list="coa_list" id="coa_display" placeholder="Pilih COA" required>
                            <datalist id="coa_list">
                                @foreach($coas as $coa)
                                <option value="{{ $coa->kode_akun }} - {{ $coa->nama_akun }}" data-id="{{ $coa->id }}"></option>
                                @endforeach
                            </datalist>
                            <input type="hidden" id="coa_id" name="coa_id" value="{{ isset($selectedCoa) ? $selectedCoa->id : '' }}">
                            <div id="coa_dropdown" class="custom-dropdown"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="bulan">Pilih Bulan</label>
                            <select name="bulan" id="bulan" class="form-control">
                                <option value="">Pilih Bulan</option>
                                @foreach ($availableMonths as $month)
                                <option value="{{ $month }}" {{ request('bulan') == $month ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $month, 1)) }}
                                </option>
                                @endforeach
                            </select>

                        </div>
                        <div class="col-md-4 align-self-end">
                            <button type="submit" class="mt-3 btn btn-secondary">Tampilkan Data</button>
                        </div>
                    </div>
                </form>

                @if(isset($periodeId) && isset($selectedCoa) && isset($bulan))

                @if(isset($entries) && count($entries) > 0)
                <div class="mb-3 text-end">
                    <a href="{{ route('admin/bukubesar/export', [
                            'periode_id' => $periodeId,
                            'coa_id' => $selectedCoa->id,
                            'bulan' => $bulan
                        ]) }}" class="btn btn-success">
                        Export Excel
                    </a>
                </div>
                @endif
                <div class="mb-4 row">
                    <h4>PERINCIAN BUKU BESAR</h4>
                    <p>Periode: {{ $periodes->find($periodeId)->nama_periode ?? 'Tidak ada periode yang dipilih' }}</p>
                    <div class="col-md-auto">Account: {{ $selectedCoa->kode_akun ?? 'Kode akun tidak ditemukan' }}</div>
                    <div class="col-md-auto">Keterangan: {{ $selectedCoa->nama_akun ?? 'Nama akun tidak ditemukan' }}</div>
                    <p>Bulan: {{ date('F', mktime(0, 0, 0, $bulan, 1)) }}</p>
                </div>
                @endif


                <!-- Ledger Entries Table -->
                @if(isset($entries) && count($entries) > 0)
                <table
                    class="table @if(isset($action) && $action === 'show_all') table-warning @elseif(isset($action) && $action === 'filter_search') table-primary @endif">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nomor Bukti</th>
                            <th>Deskripsi</th>
                            <th>Debit</th>
                            <th>Kredit</th>
                            @if(isset($action) && $action === 'show_all')
                            <th>Balance</th>
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
                            <td class="text-end">{{ $entry->debit < 0 ? '(' . number_format(abs($entry->debit), 2) . ')' : number_format($entry->debit, 2) }}</td>
                            <td class="text-end">{{ $entry->kredit < 0 ? '(' . number_format(abs($entry->kredit), 2) . ')' : number_format($entry->kredit, 2) }}</td>
                            @if(isset($action) && $action === 'show_all')
                            <td class="text-end">{{ $entry->running_total < 0 ? '(' . number_format(abs($entry->running_total), 2) . ')' : number_format($entry->running_total, 2) }}</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-end">Total:</th>
                            @if(isset($action) && $action === 'show_all')
                            <th class="text-end">{{ $finalRunningTotal < 0 ? '(' . number_format(abs($finalRunningTotal), 2) . ')' : number_format($finalRunningTotal, 2) }}</th>
                            @endif
                        </tr>
                    </tfoot>
                </table>

                @else
                <p>No data available for the selected criteria.</p>
                @endif

            </div>
        </div>
    </div>
</div>



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

            // Cari apakah ada yang cocok persis
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
            items[index].scrollIntoView({
                block: 'nearest'
            });
        }

        function selectItem(div) {
            coaInput.value = div.textContent;
            hiddenCoaInput.value = div.getAttribute('data-id');
            coaDropdown.style.display = 'none';
        }

        const rows = document.querySelectorAll('table tbody tr');

        rows.forEach((row) => {
            const nomorBukti = row.querySelector('td:nth-child(2)').textContent.trim();
            const keteranganCell = row.querySelector('td:nth-child(3)');
            const currentText = keteranganCell.textContent.trim();

            if (currentText.includes('Saldo Awal')) return;

            if (!currentText || currentText === '') {
                const merged = keteranganGabungan[nomorBukti];
                if (merged) {
                    keteranganCell.textContent = merged;
                    keteranganCell.title = merged; // tooltip opsional
                }
            }
        });
    });
</script>
@endsection
