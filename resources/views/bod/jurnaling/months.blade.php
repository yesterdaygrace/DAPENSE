@extends('layouts.bodlayout')
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
        <li class="menu-item">
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
        <li class="menu-item">
            <a href="{{ route('bod/neracasaldo/') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calculator"></i>
                <div data-i18n="Analytics">Neraca Saldo</div>
            </a>
        </li>
    </ul>
</aside>

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Period Selection -->
        <div class="mt-3 card">
            <div class="card-header">
                <h5>Pilih Periode</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('bod/jurnaling/months') }}">
                    <div class="mb-3">
                        <label for="periode-select" class="form-label">Pilih Periode</label>
                        <select name="periode_id" id="periode-select" class="form-control" required>
                            <option value="">Pilih Periode</option>
                            @foreach ($periodes as $periode)
                            <option value="{{ $periode->id }}" {{ $selectedPeriode == $periode->id ? 'selected' : '' }}>
                                {{ $periode->nama_periode }} ({{ $periode->tanggal_awal }} - {{ $periode->tanggal_akhir }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Tampilkan Bulan</button>
                </form>
            </div>
        </div>

        <!-- Months List -->

        <div class="mt-4 row">
            @foreach ($months as $month)
            <div class="col-md-4">
                <div class="card">
                    <div class="text-center card-body">
                        <h6>{{ $month['name'] }}</h6>
                        <form method="GET" action="{{ route('bod/jurnaling/showing') }}">
                            <input type="hidden" name="periode_id" value="{{ $selectedPeriode }}">
                            <input type="hidden" name="month" value="{{ $month['id'] }}">
                            <button type="submit" class="btn btn-primary">Tampilkan Jurnal</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</div>
@endsection