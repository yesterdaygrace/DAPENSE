@extends('layouts.applayout')
@section('content')
<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('bod/dashboard') }}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder ms-2">{{ Auth::user()->name }}</span>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="py-1 menu-inner">
        <!-- Dashboard -->
        <li class="menu-item active">
            <a href="{{ route('bod/dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>
        <li class="menu-item active open">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-notepad"></i>
                <div data-i18n="Layouts">Jurnaling</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('bod/jurnaling/showing') }}" class="menu-link">
                        <div data-i18n="Without menu">Tampil</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="{{ route('bod/bukubesar') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-book"></i>
                <div data-i18n="Analytics">Buku Besar</div>
            </a>
        </li>

    </ul>
</aside>

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header">
                <a href="{{ route('bod/bukubesar') }}" class="mb-3 btn btn-primary">Go Back</a>
                <h2>Buku Besar</h2>

                <!-- Form for selecting period -->
                <form action="{{ route('bod/bukubesar/searchCoaByFilter') }}" method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="periode-select" class="form-label">Select Periode</label>
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

                <!-- COA Selection Form with Date Range -->
                <form action="{{ route('bod/bukubesar/searchByDate') }}" method="GET" class="mb-3">
                    @csrf
                    <input type="hidden" name="periode_id" value="{{ $periodeId ?? '' }}">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="coa_display" class="form-label">Pilih COA</label>
                            <input type="text" class="form-control" id="coa_display" list="coa_list" placeholder="Pilih COA" required>
                            <datalist id="coa_list">
                                @foreach($coas as $coa)
                                <option value="{{ $coa->kode_akun }} - {{ $coa->nama_akun }}" data-id="{{ $coa->id }}"></option>
                                @endforeach
                            </datalist>
                            <input type="hidden" id="coa_id" name="coa_id" value="{{ isset($selectedCoa) ? $selectedCoa->id : '' }}">
                            <div id="coa_dropdown" class="custom-dropdown"></div>
                        </div>
                        <div class="col-md-4">
                            <label for="tanggal_awal" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal" required>
                        </div>
                        <div class="col-md-4">
                            <label for="tanggal_akhir" class="form-label">End Date</label>
                            <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" required>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="mt-3 btn btn-info">Filter Data</button>
                        </div>
                    </div>
                </form>

                @if(isset($periodeId) && isset($selectedCoa) && isset($tanggalAwal) && isset($tanggalAkhir))
                <div class="mb-4 row">
                    <h4>PERINCIAN BUKU BESAR</h4>
                    <p>
                        Periode: {{ $periodes->find($periodeId)->nama_periode ?? 'Tidak ada periode yang dipilih' }}
                        ({{ $tanggalAwal }} - {{ $tanggalAkhir }})
                    </p>
                    <div class="col-md-auto">Account: {{ $selectedCoa->kode_akun ?? 'Kode akun tidak ditemukan' }}</div>
                    <div class="col-md-auto">Keterangan: {{ $selectedCoa->nama_akun ?? 'Nama akun tidak ditemukan' }}</div>
                </div>
                @endif

                <!-- Ledger Entries Table -->
                @if(isset($entries) && count($entries) > 0)
                <table class="table table-primary">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Nomor Bukti</th>
                            <th>Description</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Total Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Display "Saldo Awal" -->
                        <tr>
                            <td>{{ $tanggalAwal }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format($saldoAwal > 0 ? $saldoAwal : 0, 2) }}</td>
                            <td>{{ number_format($saldoAwal < 0 ? abs($saldoAwal) : 0, 2) }}</td>
                            <td>{{ number_format($saldoAwal, 2) }}</td>
                        </tr>

                        <!-- Display journal entries -->
                        @php $runningTotal = $saldoAwal; @endphp
                        @foreach ($entries as $entry)
                        @php
                        $runningTotal += ($entry->debit - $entry->kredit);
                        @endphp
                        <tr>
                            <td>{{ $entry->tanggal_jurnal }}</td>
                            <td>{{ $entry->nomor_bukti ?? '-' }}</td>
                            <td>{{ $entry->keterangan }}</td>
                            <td>{{ number_format($entry->debit, 2) }}</td>
                            <td>{{ number_format($entry->kredit, 2) }}</td>
                            <td>{{ number_format($runningTotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5"><strong>Total Balance:</strong></td>
                            <td><strong>{{ number_format($entries->last()->running_total, 2) }}</strong></td>
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
            // Ambil data dari kolom tabel
            const debit = parseFloat(row.querySelector('td:nth-child(4)').textContent.replace(/,/g, '') || 0);
            const kredit = parseFloat(row.querySelector('td:nth-child(5)').textContent.replace(/,/g, '') || 0);
            const tanggal = row.querySelector('td:first-child').textContent.trim();
            const nomorBukti = row.querySelector('td:nth-child(2)').textContent.trim();
            const keteranganCell = row.querySelector('td:nth-child(3)'); // Kolom keterangan

            // Abaikan jika merupakan Saldo Awal
            if (keteranganCell.textContent.includes('Saldo Awal')) {
                return;
            }

            // Jika debit dan kredit bernilai 0, hanya tampilkan tanggal
            if (debit === 0 && kredit === 0) {
                keteranganCell.textContent = tanggal;
                return;
            }

            // Tentukan keterangan berdasarkan nomor bukti dan debit/kredit
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