@extends('layouts.applayout')
@section('content')
@include('components.admin-sidebar', ['activeMenu' => 'jurnaling-kasmasuk'])

<!-- Content wrapper -->
<div class="content-wrapper">
  <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
      <div class="shadow-sm card sm:rounded-lg">
        <div class="text-gray-900 card-body">
          <h1 class="mb-4">Add Periode</h1>
          <hr />
          @if (session()->has('error'))
            <div class="alert alert-danger">
              {{ session('error') }}
            </div>
          @endif
          <p><a href="{{ route('admin/jurnaling') }}" class="mb-4 btn btn-primary">Go Back</a></p>

          <form action="{{ route('admin/jurnaling/save') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
              <label for="nama_periode" class="form-label">Nama Periode</label>
              <input type="text" id="nama_periode" name="nama_periode" class="form-control @error('nama_periode') is-invalid @enderror" placeholder="Enter periode name" value="{{ old('nama_periode') }}">
              @error('nama_periode')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="mb-3">
              <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
              <input type="date" id="tanggal_awal" name="tanggal_awal" class="form-control @error('tanggal_awal') is-invalid @enderror" value="{{ old('tanggal_awal') }}">
              @error('tanggal_awal')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="mb-3">
              <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
              <input type="date" id="tanggal_akhir" name="tanggal_akhir" class="form-control @error('tanggal_akhir') is-invalid @enderror" value="{{ old('tanggal_akhir') }}">
              @error('tanggal_akhir')
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
<!-- Content wrapper -->
@endsection
