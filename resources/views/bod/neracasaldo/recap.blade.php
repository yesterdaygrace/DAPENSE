@extends('layouts.applayout')
@section('content')

<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('bod/dashboard') }}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder ms-2">{{ Auth::user()->name }}</span>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item">
            <a href="{{ route('bod/dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>
        <li class="menu-item">
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
        <li class="menu-item active">
            <a href="{{ route('bod/neracasaldo/') }}" class="menu-link">
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
                            <form method="GET" action="{{ route('bod/neracasaldo/monthstampil', ['periode' => $periode->id]) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">Tampilkan Neraca</button>
                            </form>
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