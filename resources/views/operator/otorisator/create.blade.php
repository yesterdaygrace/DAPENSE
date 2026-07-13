@extends('layouts.applayout')
@section('content')

@include('components.admin-sidebar', ['activeMenu' => 'dashboard'])

<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Tambah Otorisator</h5>
            </div>
            <div class="card-body">
                @if(Session::has('success'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('success') }}
                </div>
                @endif
                <p><a href="{{ route('operator/otorisator/home') }}" class="mb-4 btn btn-primary">Kembali</a></p>
                <form action="{{ route('operator/otorisator/save') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="nama_otorisator" class="form-label">Nama Otorisator</label>
                        <input type="text" id="nama_otorisator" name="nama_otorisator"
                            class="form-control @error('nama_otorisator') is-invalid @enderror"
                            placeholder="Masukkan nama otorisator"
                            value="{{ old('nama_otorisator') }}">
                        @error('nama_otorisator')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="jabatan_otorisator" class="form-label">Jabatan Otorisator</label>
                        <input type="text" id="jabatan_otorisator" name="jabatan_otorisator"
                            class="form-control @error('jabatan_otorisator') is-invalid @enderror"
                            placeholder="Masukkan jabatan otorisator"
                            value="{{ old('jabatan_otorisator') }}">
                        @error('jabatan_otorisator')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- / Content -->
    <div class="content-backdrop fade"></div>
</div>
@endsection