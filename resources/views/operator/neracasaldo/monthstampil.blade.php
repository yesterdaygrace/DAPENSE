@extends('layouts.applayout')
@section('title', 'Neraca Saldo - Tampil')
@section('content')

<x-dashboard.page-header
    title="Pilih Bulan"
    description="Pilih bulan untuk menampilkan Neraca Saldo"
/>

<div class="card rounded-card border border-gray-100 shadow-card mb-6">
  <div class="card-body p-4">
    <form method="GET" action="{{ route('operator/neracasaldo/monthstampil', ['periode_id' => $selectedPeriode]) }}" class="filter-row flex flex-wrap items-end gap-4">
      @csrf
      <div class="filter-group">
        <label for="periode_id" class="label">Periode</label>
        <select name="periode_id" class="select-field" onchange="this.form.submit()">
          <option value="">Pilih Periode</option>
          @foreach($periodes ?? [] as $p)
          <option value="{{ $p->id }}" {{ ($selectedPeriode ?? '') == $p->id ? 'selected' : '' }}>{{ $p->nama_periode }}</option>
          @endforeach
        </select>
      </div>
    </form>
  </div>
</div>

@if (!empty($months))
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
  @foreach ($months as $month)
  <div class="card rounded-card border border-gray-100 shadow-card flex flex-col">
    <div class="card-body flex-1 flex flex-col text-center p-5">
      <div class="w-12 h-12 rounded-2xl bg-primary-50 flex items-center justify-center mx-auto mb-3">
        <i data-lucide="calendar" class="w-6 h-6 text-primary"></i>
      </div>
      <h5 class="text-base font-semibold text-gray-900 mb-1">{{ $month['name'] }}</h5>
      <p class="text-sm text-gray-500 mb-4">Periode {{ $month['name'] }}</p>
      <form method="GET" action="{{ route('operator/neracasaldo/showing', ['periode_id' => $selectedPeriode]) }}" class="mt-auto">
        <input type="hidden" name="month" value="{{ $month['id'] }}">
        <button type="submit" class="btn-primary btn-sm w-full">Tampilkan Neraca</button>
      </form>
    </div>
  </div>
  @endforeach
</div>
@else
<div class="card rounded-card border border-gray-100 shadow-card">
  <div class="card-body">
    <x-dashboard.empty-state
        icon="calendar-x"
        title="Tidak ada data bulan"
        description="Tidak ada entri jurnal yang ditemukan untuk periode yang dipilih."
    />
  </div>
</div>
@endif

@endsection
