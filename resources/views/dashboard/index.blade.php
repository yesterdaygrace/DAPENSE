@extends('layouts.applayout')
@section('title', 'Dasbor')
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
        ['name' => 'Jurnaling', 'route' => 'bod/jurnaling/showing', 'icon' => 'file-text', 'desc' => 'Lihat jurnal transaksi'],
        ['name' => 'Buku Besar', 'route' => 'bod/bukubesar', 'icon' => 'book-open', 'desc' => 'Ringkasan akun per buku besar'],
        ['name' => 'Rekap Jurnal', 'route' => 'bod/jurnaling/showing', 'icon' => 'receipt', 'desc' => 'Rekapitulasi jurnal', 'badge' => 'NEW'],
        ['name' => 'Neraca Saldo', 'route' => 'bod/neracasaldo/', 'icon' => 'calculator', 'desc' => 'Neraca saldo periode'],
    ] as $menu)
    <x-dashboard.module-card :name="$menu['name']" :route="$menu['route']" :icon="$menu['icon']" :desc="$menu['desc']" :badge="($menu['badge'] ?? null)" />
    @endforeach
    @elseif($u === 'operator')
    @foreach([
        ['name' => 'Periode', 'route' => 'operator/periodes', 'icon' => 'calendar', 'desc' => 'Atur periode akuntansi'],
        ['name' => 'COA', 'route' => 'operator/account/coa', 'icon' => 'grid-3x3', 'desc' => 'Kode akun'],
        ['name' => 'Saldo Awal', 'route' => 'operator/saldoawal', 'icon' => 'wallet', 'desc' => 'Saldo awal periode'],
        ['name' => 'Jurnaling', 'route' => 'operator/jurnaling', 'icon' => 'file-text', 'desc' => 'Entri jurnal transaksi'],
        ['name' => 'Buku Besar', 'route' => 'operator/bukubesar', 'icon' => 'book-open', 'desc' => 'Ringkasan akun per buku besar'],
        ['name' => 'Rekap Jurnal', 'route' => $prefix . '/jurnaling/showing', 'icon' => 'receipt', 'desc' => 'Rekapitulasi jurnal'],
        ['name' => 'Neraca Saldo', 'route' => 'operator/neracasaldo/', 'icon' => 'calculator', 'desc' => 'Neraca saldo periode'],
    ] as $menu)
    <x-dashboard.module-card :name="$menu['name']" :route="$menu['route']" :icon="$menu['icon']" :desc="$menu['desc']" :badge="($menu['badge'] ?? null)" />
    @endforeach
    @else
    @foreach([
        ['name' => 'Manajemen Pengguna', 'route' => $prefix . '/products', 'icon' => 'user', 'desc' => 'Kelola pengguna sistem'],
        ['name' => 'Periode', 'route' => $prefix . '/periodes', 'icon' => 'calendar', 'desc' => 'Atur periode akuntansi'],
        ['name' => 'COA', 'route' => $prefix . '/account/coa', 'icon' => 'grid-3x3', 'desc' => 'Kode akun'],
        ['name' => 'Saldo Awal', 'route' => $prefix . '/saldoawal', 'icon' => 'wallet', 'desc' => 'Saldo awal periode'],
        ['name' => 'Jurnaling', 'route' => $prefix . '/jurnaling', 'icon' => 'file-text', 'desc' => 'Entri jurnal transaksi'],
        ['name' => 'Buku Besar', 'route' => $prefix . '/bukubesar', 'icon' => 'book-open', 'desc' => 'Ringkasan akun per buku besar'],['name' => 'Rekap Jurnal', 'route' => $prefix . '/jurnaling/showing', 'icon' => 'receipt', 'desc' => 'Rekapitulasi jurnal'],['name' => 'Neraca Saldo', 'route' => $prefix . '/neracasaldo/', 'icon' => 'calculator', 'desc' => 'Neraca saldo periode'],
    ] as $menu)
    <x-dashboard.module-card :name="$menu['name']" :route="$menu['route']" :icon="$menu['icon']" :desc="$menu['desc']" :badge="($menu['badge'] ?? null)" />
    @endforeach
    @endif
</div>

{{-- Activity & Monthly Summary --}}
<div class="grid grid-cols-1 lg:grid-cols-7 gap-6 mt-6">
    <div class="lg:col-span-4">
        <x-dashboard.activity-list :activities="$activities" />
    </div>
    <div class="lg:col-span-3">
        <x-dashboard.monthly-summary :monthlyWithTrend="$monthlySummary" />
    </div>
</div>
@endsection
