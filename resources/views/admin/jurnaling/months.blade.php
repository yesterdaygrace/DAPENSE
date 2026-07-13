@extends('layouts.applayout')
@section('content')
@include('components.admin-sidebar', ['activeMenu' => 'jurnaling-showing'])

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Period Selection -->
        <div class="mt-3 card">
            <div class="card-header">
                <h5>Pilih Periode</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin/jurnaling/months') }}">
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
                        <form method="GET" action="{{ route('admin/jurnaling/showing') }}">
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
