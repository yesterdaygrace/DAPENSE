@extends('layouts.applayout')
@section('title', 'Rekap Jurnal')
@section('content')

<x-dashboard.page-header
    title="Pilih Bulan Jurnal"
    description="Pilih bulan untuk melihat rekap jurnal"
/>

<div class="filter-card mb-6">
    <div class="card-body">
        <form method="GET" action="{{ route('admin/jurnaling/months') }}">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="periode-select" class="label">Pilih Periode</label>
                    <select name="periode_id" id="periode-select" class="select-field" required>
                        <option value="">Pilih Periode</option>
                        @foreach ($periodes as $periode)
                        <option value="{{ $periode->id }}" {{ $selectedPeriode == $periode->id ? 'selected' : '' }}>
                            {{ $periode->nama_periode }} ({{ $periode->tanggal_awal }} - {{ $periode->tanggal_akhir }})
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="btn-primary">Tampilkan Bulan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach ($months as $month)
    <div class="card">
        <div class="card-body text-center py-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $month['name'] }}</h3>
            <form method="GET" action="{{ route('admin/jurnaling/showing') }}">
                <input type="hidden" name="periode_id" value="{{ $selectedPeriode }}">
                <input type="hidden" name="month" value="{{ $month['id'] }}">
                <button type="submit" class="btn-primary btn-sm">Tampilkan Jurnal</button>
            </form>
        </div>
    </div>
    @endforeach
</div>

@endsection
