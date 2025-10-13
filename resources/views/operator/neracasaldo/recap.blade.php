@extends('layouts.operatorlayout')
@section('content')

<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('operator/dashboard') }}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder ms-2">{{ Auth::user()->name }}</span>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item">
            <a href="{{ route('operator/dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('operator/periodes') }}" class="menu-link">
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
                    <a href="{{ route('operator/account/header') }}" class="menu-link">
                        <div data-i18n="Without menu">Header</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/account/coa') }}" class="menu-link">
                        <div data-i18n="Without menu">COA</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/account/headercoa') }}" class="menu-link">
                        <div data-i18n="Without menu">Combine Header & COA</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="{{ route('operator/saldoawal') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-money"></i>
                <div data-i18n="Analytics">Saldo Awal</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-notepad"></i>
                <div data-i18n="Layouts">Jurnaling</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling') }}" class="menu-link">
                        <div data-i18n="Without menu">Kas Masuk</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling/kaskeluar') }}" class="menu-link">
                        <div data-i18n="Without menu">Kas Keluar</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling/bankmasuk') }}" class="menu-link">
                        <div data-i18n="Without menu">Bank Masuk</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling/bankkeluar') }}" class="menu-link">
                        <div data-i18n="Without menu">Bank Keluar</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling/memorial') }}" class="menu-link">
                        <div data-i18n="Without menu">Memorial</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling/memorialpenutup') }}" class="menu-link">
                        <div data-i18n="Without menu">Memorial (Penutup)</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling/showing') }}" class="menu-link">
                        <div data-i18n="Without menu">Tampil</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="{{ route('operator/bukubesar') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-book"></i>
                <div data-i18n="Analytics">Buku Besar</div>
            </a>
        </li>
        <li class="menu-item active">
            <a href="{{ route('operator/neracasaldo/') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calculator"></i>
                <div data-i18n="Analytics">Neraca Saldo</div>
            </a>
        </li>
    </ul>
</aside>

<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">

        @if(isset($periodes) && $periodes->count())
        <!-- Period Cards -->
        <div class="row">
            @foreach($periodes as $periode)
            <div class="mb-4 col-md-6 col-lg-3 d-flex">
                <div class="card flex-fill">
                    <div class="card-body">
                        <h5 class="card-title">{{ $periode->nama_periode }}</h5>
                        <p class="card-text">Start Date: {{ \Carbon\Carbon::parse($periode->tanggal_awal)->format('d M, Y') }}</p>
                        <p class="card-text">End Date: {{ \Carbon\Carbon::parse($periode->tanggal_akhir)->format('d M, Y') }}</p>
                        <div class="d-flex justify-content-between align-items-center demo-inline-spacing">
                            {{-- Initially show only the "Rekap Jurnal" and "View Journal" buttons --}}
                            @if(!$periode->is_rekap)
                            <form method="GET" action="{{ route('operator/neracasaldo/months', ['periode' => $periode->id]) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">Rekap Bulan</button>
                            </form>
                            <form method="GET" action="{{ route('operator/neracasaldo/monthstampil', ['periode' => $periode->id]) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">Tampilkan Neraca</button>
                            </form>
                            @else
                            <form method="POST" action="{{ route('operator/jurnaling/unrekap', $periode->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-warning">Unrekap</button>
                            </form>
                            <form method="GET" action="{{ route('rootsuperuser/neracasaldo/monthstampil', ['periode' => $periode->id]) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">Tampilkan Neraca</button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <!-- /Period Cards -->
        @else
        <p>Tidak ada periode yang tersedia.</p>
        @endif
    </div>
    <!-- / Content -->

    <div class="content-backdrop fade"></div>
</div>
@endsection