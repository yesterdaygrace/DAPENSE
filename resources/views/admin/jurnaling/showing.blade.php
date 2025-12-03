@extends('layouts.adminlayout')
@section('content')
<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('admin/dashboard') }}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder ms-2">{{ Auth::user()->name }}</span>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="py-1 menu-inner">
        <!-- Dashboard -->
        <li class="menu-item">
            <a href="{{ route('admin/dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('admin/products') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Analytics">User Management</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('admin/periodes') }}" class="menu-link">
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
                    <a href="{{ route('admin/account/header') }}" class="menu-link">
                        <div data-i18n="Without menu">Header</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('admin/account/coa') }}" class="menu-link">
                        <div data-i18n="Without menu">COA</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('admin/account/headercoa') }}" class="menu-link">
                        <div data-i18n="Without menu">Combine Header & COA</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="{{ route('admin/saldoawal') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-money"></i>
                <div data-i18n="Analytics">Saldo Awal</div>
            </a>
        </li>
        <li class="menu-item active open">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-notepad"></i>
                <div data-i18n="Layouts">Jurnaling</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('admin/jurnaling') }}" class="menu-link">
                        <div data-i18n="Without menu">Kas Masuk</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('admin/jurnaling/kaskeluar') }}" class="menu-link">
                        <div data-i18n="Without menu">Kas Keluar</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('admin/jurnaling/bankmasuk') }}" class="menu-link">
                        <div data-i18n="Without menu">Bank Masuk</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('admin/jurnaling/bankkeluar') }}" class="menu-link">
                        <div data-i18n="Without menu">Bank Keluar</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('admin/jurnaling/memorial') }}" class="menu-link">
                        <div data-i18n="Without menu">Memorial</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('admin/jurnaling/memorialpenutup') }}" class="menu-link">
                        <div data-i18n="Without menu">Memorial (Penutup)</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item active">
                    <a href="{{ route('admin/jurnaling/showing') }}" class="menu-link">
                        <div data-i18n="Without menu">Tampil</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="{{ route('admin/bukubesar') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-book"></i>
                <div data-i18n="Analytics">Buku Besar</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('admin/neracasaldo/') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calculator"></i>
                <div data-i18n="Analytics">Neraca Saldo</div>
            </a>
        </li>
    </ul>
</aside>

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="mt-3 card">
            <div class="card-header">
                <h5 class="card-title">Jurnal</h5>
            </div>
            <div class="card-body">
                <!-- Filters -->
                <div class="mb-3 row">
                    <div class="col">
                        <label for="filter-kategori-jurnal" class="form-label">Filter Berdasarkan Kategori Jurnal</label>
                        <div>
                            <button id="show-all" class="btn btn-secondary">Tampilkan Semua</button>
                            <button id="show-KM" class="btn btn-secondary" data-category="KM-">Kas Masuk</button>
                            <button id="show-KK" class="btn btn-secondary" data-category="KK-">Kas Keluar</button>
                            <button id="show-BM" class="btn btn-secondary" data-category="-BM-">Bank Masuk</button>
                            <button id="show-BK" class="btn btn-secondary" data-category="-BK-">Bank Keluar</button>
                            <button id="show-BK" class="btn btn-secondary" data-category="JM-">Memorial</button>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin/jurnaling/export', ['month' => request('month'), 'periode_id' => request('periode_id')]) }}"
                                class="btn btn-success">
                                Export Excel
                            </a>
                        </div>
                    </div>
                </div>

                <div class="mb-3 row">
                    <div class="col">
                        <label for="coa-search" class="form-label">Cari Nomor Bukti, COA, atau Keterangan</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text" id="basic-addon-search31"><i class="bx bx-search"></i></span>
                            <input type="text" id="search-field" class="form-control" placeholder="Cari Nomor Bukti, COA, atau Keterangan">
                        </div>
                    </div>
                </div>
                @if (isset($monthEntries) && $monthEntries->isEmpty())
                <p>No journal entries available for {{ $monthName }}.</p>
                @elseif (isset($monthEntries))
                <table class="table table-bordered" id="journal-table">
                    <thead>
                        <tr align="center">
                            <th id="sort-tanggal-jurnal" style="cursor: pointer;" data-sort="asc">
                                Tanggal Jurnal
                                <i id="sort-icon" class="sort-icon bx bx-sort-down"></i>
                            </th>
                            <th>Nomor Bukti</th>
                            <th>Keterangan</th>
                            <th>Kategori Jurnal</th>
                            <th>COA</th>
                            <th>Debit</th>
                            <th>Kredit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $totalDebit = 0;
                        $totalCredit = 0;
                        @endphp

                        @foreach ($monthEntries as $entry)
                        @php
                        $totalDebit += $entry->debit;
                        $totalCredit += $entry->kredit;
                        @endphp
                        <tr class="journal-row">
                            <td>{{ $entry->tanggal_jurnal }}</td>
                            <td>{{ $entry->nomor_bukti }}</td>
                            <td>{{ $entry->keterangan }}</td>
                            <td>{{ $entry->kategori_jurnal }}</td>
                            <td>{{ $entry->coa->kode_akun }} - {{ $entry->coa->nama_akun }}</td>
                            <td>{{ number_format($entry->debit, 2) }}</td>
                            <td>{{ number_format($entry->kredit, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" align="right">Total:</th>
                            <th>{{ number_format($totalDebit, 2) }}</th>
                            <th>{{ number_format($totalCredit, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
                @else
                <p>No data found.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterButtons = document.querySelectorAll('button[data-category], #show-all');
        const searchField = document.getElementById('search-field');
        const journalTable = document.getElementById('journal-table');
        const sortButton = document.getElementById('sort-tanggal-jurnal');

        const rows = document.querySelectorAll('.journal-row');
        let previousNomorBukti = null;

        rows.forEach(row => {
            const nomorBukti = row.querySelector('td:nth-child(2)').innerText.trim();
            if (previousNomorBukti && previousNomorBukti !== nomorBukti) {
                row.style.borderTop = "3px solid #000000"; // Garis pemisah saat nomor bukti berubah
            }
            previousNomorBukti = nomorBukti;
        });

        // Fungsi Filter
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const category = this.getAttribute('data-category') || 'all';
                filterByCategory(category);
            });
        });

        function filterByCategory(category) {
            const rows = journalTable.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const nomorBuktiCell = row.querySelector('td:nth-child(2)');
                const nomorBukti = nomorBuktiCell ? nomorBuktiCell.textContent.trim() : '';

                if (category === 'all') {
                    row.style.display = '';
                } else if (category === 'KM-' || category === 'KK-' || category === 'JM-') {
                    row.style.display = nomorBukti.startsWith(category) ? '' : 'none';
                } else {
                    row.style.display = nomorBukti.includes(category) ? '' : 'none';
                }
            });
        }

        // Fungsi Pencarian
        searchField.addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('.journal-row');

            rows.forEach(row => {
                const nomorBukti = row.querySelector('td:nth-child(2)').innerText.toLowerCase();
                const coa = row.querySelector('td:nth-child(5)').innerText.toLowerCase();
                const keterangan = row.querySelector('td:nth-child(3)').innerText.toLowerCase();

                row.style.display = (nomorBukti.includes(query) || coa.includes(query) || keterangan.includes(query)) ? '' : 'none';
            });
        });

        // Fungsi Sorting
        sortButton.addEventListener('click', function() {
            const order = this.getAttribute('data-sort') === 'asc' ? 'desc' : 'asc';
            this.setAttribute('data-sort', order);
            const icon = document.getElementById('sort-icon');
            icon.className = order === 'asc' ? 'sort-icon bx bx-sort-up' : 'sort-icon bx bx-sort-down';

            sortTable(order);
        });

        function sortTable(order) {
            const tbody = journalTable.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr.journal-row'));
            rows.sort((a, b) => {
                const dateA = new Date(a.querySelector('td:first-child').innerText);
                const dateB = new Date(b.querySelector('td:first-child').innerText);
                return order === 'asc' ? dateA - dateB : dateB - dateA;
            });
            rows.forEach(row => tbody.appendChild(row));
        }
    });
</script>


@endsection