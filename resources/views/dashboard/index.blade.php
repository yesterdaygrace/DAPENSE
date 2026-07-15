@extends('layouts.applayout')
@section('title', 'Dashboard')
@section('content')

@php
$u = Auth::user()->usertype;
$prefix = match($u) { 'rootsuperuser' => 'rootsuperuser', 'bod' => 'bod', 'operator' => 'operator', default => 'admin' };
$roleLabel = match($u) { 'rootsuperuser' => 'Root Superuser', 'bod' => 'BOD', 'operator' => 'Operator', default => 'Admin' };
@endphp

<x-dashboard.hero :roleLabel="$roleLabel" />

{{-- KPI Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mt-6">
    <x-dashboard.kpi-card icon="file-text" title="Total Jurnal" :value="number_format($stats->total_entries ?? 0, 0, ',', '.')" :trend="($stats->entries_trend ?? null)" color="#1D4ED8" bg="rgba(29,78,216,0.08)" />
    <x-dashboard.kpi-card icon="arrow-down-circle" title="Total Debit" :value="'Rp ' . number_format($stats->total_debit ?? 0, 0, ',', '.')" :trend="($stats->debit_trend ?? null)" color="#16A34A" bg="rgba(22,163,74,0.08)" />
    <x-dashboard.kpi-card icon="arrow-up-circle" title="Total Kredit" :value="'Rp ' . number_format($stats->total_kredit ?? 0, 0, ',', '.')" :trend="($stats->kredit_trend ?? null)" color="#DC2626" bg="rgba(220,38,38,0.08)" />
    <x-dashboard.kpi-card icon="calendar" title="Periode Aktif" :value="$periodeAktif->nama_periode ?? '—'" color="#F59E0B" bg="rgba(245,158,11,0.08)" />
</div>

{{-- Module Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mt-6">
    @if($u === 'bod')
    @foreach([
        ['name' => 'Jurnaling', 'route' => 'bod/jurnaling/showing', 'icon' => 'bx bx-notepad', 'desc' => 'Lihat jurnal transaksi'],
        ['name' => 'Buku Besar', 'route' => 'bod/bukubesar', 'icon' => 'bx bx-book', 'desc' => 'Ringkasan akun per buku besar'],
        ['name' => 'Rekap Jurnal', 'route' => 'bod/jurnaling/showing', 'icon' => 'bx bx-receipt', 'desc' => 'Rekapitulasi jurnal', 'badge' => 'NEW'],
        ['name' => 'Neraca Saldo', 'route' => 'bod/neracasaldo/', 'icon' => 'bx bx-calculator', 'desc' => 'Neraca saldo periode'],
    ] as $menu)
    <x-dashboard.module-card :name="$menu['name']" :route="$menu['route']" :icon="$menu['icon']" :desc="$menu['desc']" :badge="($menu['badge'] ?? null)" />
    @endforeach
    @elseif($u === 'operator')
    @foreach([
        ['name' => 'Periode', 'route' => 'operator/periodes', 'icon' => 'bx bx-calendar', 'desc' => 'Atur periode akuntansi'],
        ['name' => 'COA', 'route' => 'operator/account/coa', 'icon' => 'bx bx-spreadsheet', 'desc' => 'Chart of Accounts'],
        ['name' => 'Saldo Awal', 'route' => 'operator/saldoawal', 'icon' => 'bx bx-money', 'desc' => 'Saldo awal periode'],
        ['name' => 'Jurnaling', 'route' => 'operator/jurnaling', 'icon' => 'bx bx-notepad', 'desc' => 'Entri jurnal transaksi'],
        ['name' => 'Buku Besar', 'route' => 'operator/bukubesar', 'icon' => 'bx bx-book', 'desc' => 'Ringkasan akun per buku besar'],
        ['name' => 'Rekap Jurnal', 'route' => 'operator/jurnaling/showing', 'icon' => 'bx bx-receipt', 'desc' => 'Rekapitulasi jurnal', 'badge' => 'NEW'],
        ['name' => 'Neraca Saldo', 'route' => 'operator/neracasaldo/', 'icon' => 'bx bx-calculator', 'desc' => 'Neraca saldo periode'],
    ] as $menu)
    <x-dashboard.module-card :name="$menu['name']" :route="$menu['route']" :icon="$menu['icon']" :desc="$menu['desc']" :badge="($menu['badge'] ?? null)" />
    @endforeach
    @else
    @foreach([
        ['name' => 'User Management', 'route' => $prefix . '/products', 'icon' => 'bx bx-user', 'desc' => 'Kelola pengguna sistem'],
        ['name' => 'Periode', 'route' => $prefix . '/periodes', 'icon' => 'bx bx-calendar', 'desc' => 'Atur periode akuntansi'],
        ['name' => 'COA', 'route' => $prefix . '/account/coa', 'icon' => 'bx bx-spreadsheet', 'desc' => 'Chart of Accounts'],
        ['name' => 'Saldo Awal', 'route' => $prefix . '/saldoawal', 'icon' => 'bx bx-money', 'desc' => 'Saldo awal periode'],
        ['name' => 'Jurnaling', 'route' => $prefix . '/jurnaling', 'icon' => 'bx bx-notepad', 'desc' => 'Entri jurnal transaksi'],
        ['name' => 'Buku Besar', 'route' => $prefix . '/bukubesar', 'icon' => 'bx bx-book', 'desc' => 'Ringkasan akun per buku besar'],
        ['name' => 'Rekap Jurnal', 'route' => $prefix . '/jurnaling/showing', 'icon' => 'bx bx-receipt', 'desc' => 'Rekapitulasi jurnal', 'badge' => 'NEW'],
        ['name' => 'Neraca Saldo', 'route' => $prefix . '/neracasaldo/', 'icon' => 'bx bx-calculator', 'desc' => 'Neraca saldo periode'],
    ] as $menu)
    <x-dashboard.module-card :name="$menu['name']" :route="$menu['route']" :icon="$menu['icon']" :desc="$menu['desc']" :badge="($menu['badge'] ?? null)" />
    @endforeach
    @endif
</div>

{{-- Activity & Monthly Summary --}}
<div class="grid grid-cols-1 lg:grid-cols-7 gap-6 mt-6">
    <div class="lg:col-span-4">
        <x-dashboard.activity-list :activities="$activities" :routePrefix="$prefix" />
    </div>
    <div class="lg:col-span-3">
        <x-dashboard.monthly-summary :monthlyWithTrend="$monthlySummary" />
    </div>
</div>
@endsection
