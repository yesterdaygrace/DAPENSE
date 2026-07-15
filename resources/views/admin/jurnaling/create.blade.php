@extends('layouts.applayout')
@section('title', 'Jurnaling - Tambah')
@section('content')

<x-dashboard.page-header
    title="Tambah Periode"
    description="Buat periode akuntansi baru"
    :actions="'<a href=\'' . route('admin/jurnaling') . '\' class=\'btn-secondary\'>Kembali</a>'"
/>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin/jurnaling/save') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label for="nama_periode" class="label">Nama Periode</label>
                    <input type="text" id="nama_periode" name="nama_periode" class="input-field @error('nama_periode') border-danger @enderror" placeholder="Contoh: 2024" value="{{ old('nama_periode') }}">
                    @error('nama_periode')
                        <p class="text-sm text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="tanggal_awal" class="label">Tanggal Awal</label>
                    <input type="date" id="tanggal_awal" name="tanggal_awal" class="input-field @error('tanggal_awal') border-danger @enderror" value="{{ old('tanggal_awal') }}">
                    @error('tanggal_awal')
                        <p class="text-sm text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="tanggal_akhir" class="label">Tanggal Akhir</label>
                    <input type="date" id="tanggal_akhir" name="tanggal_akhir" class="input-field @error('tanggal_akhir') border-danger @enderror" value="{{ old('tanggal_akhir') }}">
                    @error('tanggal_akhir')
                        <p class="text-sm text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="btn-primary">Simpan Periode</button>
            </div>
        </form>
    </div>
</div>

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
