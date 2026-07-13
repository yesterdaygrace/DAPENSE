@extends('layouts.applayout')
@section('content')

@include('components.admin-sidebar', ['activeMenu' => 'periode'])

<!-- Content wrapper -->
<div class="content-wrapper">
  <!-- Content -->

  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="shadow-sm card sm:rounded-lg">
      <div class="text-gray-900 card-body">
        <h1 class="mb-4">Tambah Periode</h1>
        <hr />
        @if (session()->has('error'))
        <div class="alert alert-danger">
          {{ session('error') }}
        </div>
        @endif
        <p><a href="{{ route('operator/periodes') }}" class="mb-4 btn btn-primary">Kembali</a></p>

        <form action="{{ route('operator/periodes/save') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="mb-3">
            <label for="nama_periode" class="form-label">Nama Periode</label>
            <input type="text" id="nama_periode" name="nama_periode" minlength="4" maxlength="4" class="form-control @error('nama_periode') is-invalid @enderror" placeholder="Masukkan nama periode" value="{{ old('nama_periode') }}">
            @error('nama_periode')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label for="tanggal_awal" class="form-label">Tanggal Awal</label>
            <input type="date" id="tanggal_awal" name="tanggal_awal" class="form-control" readonly>
          </div>

          <div class="mb-3">
            <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
            <input type="date" id="tanggal_akhir" name="tanggal_akhir" class="form-control" readonly>
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

<script>
  const namaPeriodeInput = document.getElementById('nama_periode');
  const tanggalAwalInput = document.getElementById('tanggal_awal');
  const tanggalAkhirInput = document.getElementById('tanggal_akhir');

  namaPeriodeInput.addEventListener('input', function() {
    const tahun = this.value.trim();
    if (/^\d{4}$/.test(tahun)) {
      tanggalAwalInput.value = `${tahun}-01-01`;
      tanggalAkhirInput.value = `${tahun}-12-31`;
    } else {
      tanggalAwalInput.value = '';
      tanggalAkhirInput.value = '';
    }
  });
</script>
@endsection