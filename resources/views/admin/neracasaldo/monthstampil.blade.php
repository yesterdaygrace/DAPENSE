@extends('layouts.applayout')
@section('content')
@include('components.admin-sidebar', ['activeMenu' => 'neracasaldo'])

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Months List -->
        @if (!empty($months))
        <div class="mt-4 row">
            @foreach ($months as $month)
            <div class="col-md-4">
                <div class="card">
                    <div class="text-center card-body">
                        <h6>{{ $month['name'] }}</h6>
                        <form method="GET" action="{{ route('admin/neracasaldo/showing', ['periode_id' => $selectedPeriode]) }}">
                            <input type="hidden" name="month" value="{{ $month['id'] }}">
                            <button type="submit" class="btn btn-primary">Tampilkan Neraca</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="mt-4">Tidak ada entri jurnal yang ditemukan untuk periode yang dipilih.</p>
        @endif

    </div>
</div>

@endsection