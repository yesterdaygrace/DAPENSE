@extends('layouts.applayout')
@section('title', 'Pengaturan - Edit')
@section('content')

<x-dashboard.page-header
  title="Edit Otorisator"
  description="Ubah data otorisator transaksi"
  :actions="'<a href=\"' . route('rootsuperuser/otorisator/home') . '\" class=\"btn-secondary\"><i data-lucide=\"arrow-left\" class=\"w-4 h-4\"></i> Kembali</a>'"
/>

<div class="card">
  <div class="card-body">
    <form action="{{ route('rootsuperuser/otorisator/update', $otorisator->id) }}" method="POST">
      @csrf
      @method('PUT')
      <div class="mb-4">
        <label for="nama_otorisator" class="label">Nama Otorisator</label>
        <input type="text" id="nama_otorisator" name="nama_otorisator"
          class="input-field @error('nama_otorisator') border-danger @enderror"
          placeholder="Masukkan nama otorisator"
          value="{{ old('nama_otorisator', $otorisator->nama_otorisator) }}">
        @error('nama_otorisator')
        <p class="text-sm text-danger mt-1">{{ $message }}</p>
        @enderror
      </div>
      <div class="mb-4">
        <label for="jabatan_otorisator" class="label">Jabatan Otorisator</label>
        <input type="text" id="jabatan_otorisator" name="jabatan_otorisator"
          class="input-field @error('jabatan_otorisator') border-danger @enderror"
          placeholder="Masukkan jabatan otorisator"
          value="{{ old('jabatan_otorisator', $otorisator->jabatan_otorisator) }}">
        @error('jabatan_otorisator')
        <p class="text-sm text-danger mt-1">{{ $message }}</p>
        @enderror
      </div>
      <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary">Update</button>
      </div>
    </form>
  </div>
</div>

@endsection
