@extends('layouts.applayout')
@section('title', 'Posting')
@section('content')

<x-dashboard.page-header
    title="Posting"
    description="Posting jurnal transaksi ke buku besar"
/>

@if(is_object($selectedPeriode))

<div class="card mb-6">
    <div class="card-body">
        <form action="{{ route('rootsuperuser/posting') }}" method="GET" class="row">
            <div class="col-md-6">
                <label for="periode" class="label">Select Period</label>
                <select class="select-field" id="periode_id_form" name="periode_id"
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
                <label for="search" class="label">Search COA or Description</label>
                <div class="input-group">
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                        class="input-field" placeholder="Search COA or Description">
                    <button type="submit" class="btn-secondary btn-sm">Search</button>
                </div>
            </div>
        </form>
    </div>
</div>

@if(count($monthEntries) > 0)

@php
$totalJurnalCount = 0;
foreach($monthEntries as $entries) { $totalJurnalCount += $entries->count(); }
@endphp

<div class="card mb-6">
    <div class="card-body">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-4">
                <div>
                    <span class="block text-gray-500 text-xs uppercase tracking-wide mb-0.5">Periode</span>
                    <span class="font-semibold text-gray-900">{{ $selectedPeriode->nama_periode }}</span>
                </div>
                <div>
                    <span class="block text-gray-500 text-xs uppercase tracking-wide mb-0.5">COA Accounts</span>
                    <span class="font-semibold text-gray-900">{{ $totalJurnalCount }}</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="badge badge-warning">Draft</span>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="accordion">
            @foreach($monthEntries as $monthNumber => $entries)
            <div class="accordion-item" x-data="{ open: false }">
                <h2 class="accordion-header">
                    <button class="accordion-button" :class="{ 'collapsed': !open }" @click="open = !open" type="button" :aria-expanded="open">
                        {{ $months[$monthNumber] }} - {{ count($entries) }} COA Accounts
                    </button>
                </h2>
                <div x-show="open" x-collapse.duration.200ms>
                    <div class="accordion-body">
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Account</th>
                                        <th>Total Debit</th>
                                        <th>Total Credit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $monthTotalDebit = 0;
                                    $monthTotalCredit = 0;
                                    $previousCategory = null;
                                    @endphp

                                    @foreach($entries as $entry)
                                    @if ($entry->coa->kategori !== $previousCategory)
                                    <tr>
                                        <td colspan="3" class="font-weight-bold">{{ $entry->coa->kategori }}</td>
                                    </tr>
                                    @php $previousCategory = $entry->coa->kategori; @endphp
                                    @endif

                                    <tr>
                                        <td>{{ $entry->coa->kode_akun }} - {{ $entry->coa->nama_akun }}</td>
                                        <td>{{ number_format($entry->total_debit, 2) }}</td>
                                        <td>{{ number_format($entry->total_kredit, 2) }}</td>
                                    </tr>

                                    @php
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

@else

<x-dashboard.empty-state
    icon="book-open"
    title="Tidak Ada Data Jurnal"
    description="Tidak ada entri jurnal untuk periode yang dipilih."
/>

@endif

@else

<x-dashboard.empty-state
    icon="calendar"
    title="Pilih Periode"
    description="Silakan pilih periode untuk melihat data jurnal."
/>

@endif

@endsection
