@extends('layouts.applayout')
@section('content')
@include('components.admin-sidebar', ['activeMenu' => 'neracasaldo'])

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
                            <form method="GET" action="{{ route('admin/neracasaldo/months', ['periode' => $periode->id]) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">Rekap Bulan</button>
                            </form>
                            <form method="GET" action="{{ route('admin/neracasaldo/monthstampil', ['periode' => $periode->id]) }}">
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