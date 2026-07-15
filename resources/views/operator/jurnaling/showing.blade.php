@extends('layouts.applayout')
@section('title', 'Rekap Jurnal')
@section('content')

<x-dashboard.page-header
    title="Rekap Jurnal"
    description="Daftar semua entri jurnal periode ini"
    :actions="'<a href=\'' . route('operator/jurnaling/export', ['month' => request('month'), 'periode_id' => request('periode_id')]) . '\' class=\'btn-success\'>Export Excel</a>'"
/>

<div class="filter-card mb-6">
    <div class="card-body">
        <div class="filter-row">
            <div class="filter-group flex-1">
                <label class="label">Filter Kategori Jurnal</label>
                <div class="flex flex-wrap gap-2">
                    <button id="show-all" class="btn-secondary btn-sm">Semua</button>
                    <button id="show-KM" class="btn-secondary btn-sm" data-category="KM-">Kas Masuk</button>
                    <button id="show-KK" class="btn-secondary btn-sm" data-category="KK-">Kas Keluar</button>
                    <button id="show-BM" class="btn-secondary btn-sm" data-category="-BM-">Bank Masuk</button>
                    <button id="show-BK" class="btn-secondary btn-sm" data-category="-BK-">Bank Keluar</button>
                    <button id="show-JM" class="btn-secondary btn-sm" data-category="JM-">Memorial</button>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <div class="relative max-w-sm">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                <input type="text" id="search-field" class="input-field pl-10" placeholder="Cari Nomor Bukti, COA, atau Keterangan">
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        @if (isset($monthEntries) && $monthEntries->isEmpty())
        <div class="card-body">
            <x-dashboard.empty-state
                icon="book-open"
                title="Tidak ada data jurnal"
                description="Belum ada entri jurnal untuk bulan {{ $monthName }}."
            />
        </div>
        @elseif (isset($monthEntries))
        <div class="overflow-x-auto">
            <table class="data-table" id="journal-table">
                <thead>
                    <tr>
                        <th class="sticky top-0 bg-gray-50 z-10" id="sort-tanggal-jurnal" class="cursor-pointer" data-sort="asc">
                            Tanggal Jurnal
                            <i id="sort-icon" class="bx bx-sort-down ml-1"></i>
                        </th>
                        <th class="sticky top-0 bg-gray-50 z-10">Nomor Bukti</th>
                        <th class="sticky top-0 bg-gray-50 z-10">Keterangan</th>
                        <th class="sticky top-0 bg-gray-50 z-10">Kategori Jurnal</th>
                        <th class="sticky top-0 bg-gray-50 z-10">COA</th>
                        <th class="sticky top-0 bg-gray-50 z-10 num-col">Debit</th>
                        <th class="sticky top-0 bg-gray-50 z-10 num-col">Kredit</th>
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
                    <tr class="journal-row hover:bg-gray-50/50 transition-colors">
                        <td>{{ $entry->tanggal_jurnal }}</td>
                        <td class="font-mono text-sm">{{ $entry->nomor_bukti }}</td>
                        <td>{{ $entry->keterangan }}</td>
                        <td>
                            <span class="badge">{{ $entry->kategori_jurnal }}</span>
                        </td>
                        <td>{{ $entry->coa->kode_akun }} - {{ $entry->coa->nama_akun }}</td>
                        <td class="num-col">{{ number_format($entry->debit, 2) }}</td>
                        <td class="num-col">{{ number_format($entry->kredit, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 font-semibold">
                        <th colspan="5" class="text-right">Total:</th>
                        <th id="total-debit" class="num-col">{{ number_format($totalDebit, 2) }}</th>
                        <th id="total-credit" class="num-col">{{ number_format($totalCredit, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="card-body">
            <x-dashboard.empty-state
                icon="alert-circle"
                title="Tidak ada data"
                description="Data tidak ditemukan."
            />
        </div>
        @endif
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
                row.style.borderTop = "3px solid #000000";
            }
            previousNomorBukti = nomorBukti;
        });

        function updateTotals() {
            const rows = journalTable.querySelectorAll('tbody tr.journal-row');
            let totalDebit = 0;
            let totalCredit = 0;

            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    const debit = parseFloat(row.querySelector('td:nth-child(6)').innerText.replace(/,/g, '')) || 0;
                    const kredit = parseFloat(row.querySelector('td:nth-child(7)').innerText.replace(/,/g, '')) || 0;
                    totalDebit += debit;
                    totalCredit += kredit;
                }
            });

            document.getElementById('total-debit').innerText = totalDebit.toLocaleString(undefined, {
                minimumFractionDigits: 2
            });
            document.getElementById('total-credit').innerText = totalCredit.toLocaleString(undefined, {
                minimumFractionDigits: 2
            });
        }

        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const category = this.getAttribute('data-category') || 'all';
                filterByCategory(category);
                updateTotals();
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

        searchField.addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('.journal-row');

            rows.forEach(row => {
                const nomorBukti = row.querySelector('td:nth-child(2)').innerText.toLowerCase();
                const coa = row.querySelector('td:nth-child(5)').innerText.toLowerCase();
                const keterangan = row.querySelector('td:nth-child(3)').innerText.toLowerCase();

                row.style.display = (nomorBukti.includes(query) || coa.includes(query) || keterangan.includes(query)) ? '' : 'none';
            });

            updateTotals();
        });

        sortButton.addEventListener('click', function() {
            const order = this.getAttribute('data-sort') === 'asc' ? 'desc' : 'asc';
            this.setAttribute('data-sort', order);
            const icon = document.getElementById('sort-icon');
            icon.className = order === 'asc' ? 'bx bx-sort-up' : 'bx bx-sort-down';

            sortTable(order);
            updateTotals();
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

        updateTotals();
    });
</script>

@endsection
