@extends('layouts.applayout')
@section('title', 'Neraca Saldo - Rekap')
@section('content')

<x-dashboard.page-header
    title="Rekap Neraca Saldo"
    description="Ringkasan neraca saldo seluruh periode"
/>

@if(isset($periodes) && $periodes->count())
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    @foreach($periodes as $periode)
    <div class="card rounded-card border border-gray-100 shadow-card flex flex-col hover:shadow-card-hover transition-shadow">
        <div class="card-body flex-1 flex flex-col p-5">
            <div class="w-12 h-12 rounded-2xl bg-primary-50 flex items-center justify-center mb-4">
                <i data-lucide="calendar" class="w-6 h-6 text-primary"></i>
            </div>
            <h5 class="text-base font-bold text-gray-900 mb-1">{{ $periode->nama_periode }}</h5>
            <p class="text-sm text-gray-500 mb-1">{{ \Carbon\Carbon::parse($periode->tanggal_awal)->format('d M, Y') }} — {{ \Carbon\Carbon::parse($periode->tanggal_akhir)->format('d M, Y') }}</p>
            <div class="flex items-center gap-2 mt-3">
                <form method="GET" action="{{ route('operator/neracasaldo/months', ['periode' => $periode->id]) }}" class="contents">
                    @csrf
                    <button type="submit" class="btn-primary btn-sm flex-1">Rekap Bulan</button>
                </form>
                <form method="GET" action="{{ route('operator/neracasaldo/monthstampil', ['periode' => $periode->id]) }}" class="contents">
                    @csrf
                    <button type="submit" class="btn-secondary btn-sm flex-1">Tampilkan Neraca</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="card rounded-card border border-gray-100 shadow-card">
    <div class="card-body">
        <x-dashboard.empty-state
            icon="folder-open"
            title="Tidak ada periode"
            description="Tidak ada periode yang tersedia."
        />
    </div>
</div>
@endif

@endsection
