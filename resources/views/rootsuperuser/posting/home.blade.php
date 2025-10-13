@extends('layouts.rootsuperuserlayout')
@section('content')
<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('rootsuperuser/dashboard') }}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder ms-2">{{ Auth::user()->name }}</span>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="py-1 menu-inner">
        <!-- Dashboard -->
        <li class="menu-item">
            <a href="{{ route('rootsuperuser/dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('rootsuperuser/products') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div data-i18n="Analytics">User Management</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div data-i18n="Layouts">Accounts</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('rootsuperuser/account/header') }}" class="menu-link">
                        <div data-i18n="Without menu">Header</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('rootsuperuser/account/coa') }}" class="menu-link">
                        <div data-i18n="Without menu">COA</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('rootsuperuser/account/headercoa') }}" class="menu-link">
                        <div data-i18n="Without menu">Combine Header & COA</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="{{ route('rootsuperuser/jurnaling') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div data-i18n="Analytics">Jurnaling</div>
            </a>
        </li>
        <li class="menu-item active">
            <a href="{{ route('rootsuperuser/posting') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div data-i18n="Analytics">Posting Jurnal</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('rootsuperuser/bukubesar') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div data-i18n="Analytics">Buku Besar</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('rootsuperuser/saldoawal') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div data-i18n="Analytics">Saldo Awal</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('rootsuperuser/dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-dock-top"></i>
                <div data-i18n="Analytics">Rekap Jurnal</div>
            </a>
        </li>

    </ul>
</aside>


@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="mt-3 card">
            <div class="card-header d-flex align-items-center justify-content-between">
                @if(is_object($selectedPeriode))
                <h5 class="mb-0">Posting</h5>
                @else
                @endif
            </div>
            <div class="card-body">
                <!-- Period Selection Form -->
                <form action="{{ route('rootsuperuser/posting') }}" method="GET" class="mb-3 row">
                    <div class="col-md-6">
                        <label for="periode" class="form-label">Select Period</label>
                        <select class="form-control" id="periode_id_form" name="periode_id"
                            onchange="this.form.submit()">
                            @foreach ($periodes as $periode)
                            <option value="{{ $periode->id }}" {{ request()->get('periode_id',
                                session('selectedPeriode'))
                                == $periode->id ? 'selected' : '' }}>
                                {{ $periode->nama_periode }} ({{ $periode->tanggal_awal }} - {{ $periode->tanggal_akhir
                                }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="search" class="form-label">Search COA or Description</label>
                        <div class="input-group">
                            <input type="text" id="search" name="search" value="{{ request('search') }}"
                                class="form-control" placeholder="Search COA or Description">
                            <button type="submit" class="btn btn-secondary">Search</button>
                        </div>
                    </div>
                </form>
                <div class="accordion" id="accordionExample">
                    @foreach($monthEntries as $monthNumber => $entries)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading{{ $monthNumber }}">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse{{ $monthNumber }}" aria-expanded="false"
                                aria-controls="collapse{{ $monthNumber }}">
                                {{ $months[$monthNumber] }} - {{ count($entries) }} COA Accounts
                            </button>
                        </h2>
                        <div id="collapse{{ $monthNumber }}" class="accordion-collapse collapse"
                            aria-labelledby="heading{{ $monthNumber }}" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Account</th>
                                            <th>Total Debit</th>
                                            <th>Total Credit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        // Inisialisasi total untuk bulan ini
                                        $monthTotalDebit = 0;
                                        $monthTotalCredit = 0;
                                        $previousCategory = null; // Variabel untuk menyimpan kategori sebelumnya
                                        @endphp

                                        @foreach($entries as $entry)
                                        @if ($entry->coa->kategori !== $previousCategory)
                                        <tr>
                                            <td colspan="3" class="font-weight-bold">{{ $entry->coa->kategori }}</td> <!-- Menampilkan kategori -->
                                        </tr>
                                        @php $previousCategory = $entry->coa->kategori; @endphp
                                        @endif

                                        <tr>
                                            <td>{{ $entry->coa->kode_akun }} - {{ $entry->coa->nama_akun }}</td>
                                            <td>{{ number_format($entry->total_debit, 2) }}</td>
                                            <td>{{ number_format($entry->total_kredit, 2) }}</td>
                                        </tr>

                                        @php
                                        // Menjumlahkan total debit dan kredit untuk bulan ini
                                        $monthTotalDebit += $entry->total_debit;
                                        $monthTotalCredit += $entry->total_kredit;
                                        @endphp
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Total Balance</th>
                                            <th>{{ number_format($monthTotalDebit,2) }}</th>
                                            <th>{{ number_format($monthTotalCredit,2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const totalDebitElement = document.getElementById("total-debit");
        const totalKreditElement = document.getElementById("total-kredit");

        function calculateGroupedTotals() {
            // Object to hold sums by grouping criteria
            const groupedData = {};

            // Get all rows from the table
            const rows = document.querySelectorAll('#jurnaling-table tbody tr');

            rows.forEach(function(row) {
                const date = row.querySelector('td:nth-child(1)').textContent.trim();
                const description = row.querySelector('td:nth-child(2)').textContent.trim();
                const category = row.querySelector('td:nth-child(3)').textContent.trim();
                const account = row.querySelector('td:nth-child(4)').textContent.trim();
                const debit = parseFloat(row.querySelector('td:nth-child(5)').textContent.trim()) || 0;
                const kredit = parseFloat(row.querySelector('td:nth-child(6)').textContent.trim()) || 0;

                // Create a unique key based on Date, Description, Category, and Account
                const key = `${date}-${description}-${category}-${account}`;

                // If this grouping already exists, sum the debit and credit
                if (groupedData[key]) {
                    groupedData[key].debit += debit;
                    groupedData[key].kredit += kredit;
                } else {
                    // Otherwise, initialize the group
                    groupedData[key] = {
                        debit: debit,
                        kredit: kredit
                    };
                }
            });

            // Sum up all grouped debits and credits
            let totalDebit = 0;
            let totalKredit = 0;

            for (const key in groupedData) {
                totalDebit += groupedData[key].debit;
                totalKredit += groupedData[key].kredit;
            }

            // Update the displayed totals
            totalDebitElement.textContent = totalDebit.toFixed(0); // Or adjust for 2 decimal places if needed
            totalKreditElement.textContent = totalKredit.toFixed(0);
        }

        // Initial call to calculate totals on page load
        calculateGroupedTotals();
    });
</script>
