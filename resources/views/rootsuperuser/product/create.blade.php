@extends('layouts.applayout')
@section('title', 'User - Tambah')
@section('content')

<x-dashboard.page-header
  title="Tambah User"
  description="Buat akun pengguna baru"
  :actions="'<a href=\"' . route('rootsuperuser/products') . '\" class=\"btn-secondary\"><i data-lucide=\"arrow-left\" class=\"w-4 h-4\"></i> Kembali</a>'"
/>

<div class="card">
  <div class="card-header">
    <h3 class="text-base font-semibold text-gray-900">Informasi Pengguna</h3>
    <p class="text-sm text-gray-500">Lengkapi data berikut untuk menambahkan pengguna baru.</p>
  </div>
  <div class="card-body">
    <form action="{{ route('rootsuperuser/products/save') }}" method="POST" enctype="multipart/form-data">
      @csrf
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="mb-4">
          <label for="name" class="label">Nama</label>
          <input type="text" id="name" name="name" class="input-field @error('name') border-danger @enderror" placeholder="Masukkan nama" value="{{ old('name') }}">
          @error('name')
          <p class="text-sm text-danger mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div class="mb-4">
          <label for="email" class="label">Email</label>
          <input type="email" id="email" name="email" class="input-field @error('email') border-danger @enderror" placeholder="Masukkan email" value="{{ old('email') }}">
          @error('email')
          <p class="text-sm text-danger mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div class="mb-4">
          <label for="usertype" class="label">User Type</label>
          <select id="usertype" name="usertype" class="select-field @error('usertype') border-danger @enderror">
            <option value="">Select User Type</option>
            <option value="admin" {{ old('usertype') == 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="operator" {{ old('usertype') == 'operator' ? 'selected' : '' }}>Operator</option>
            <option value="bod" {{ old('usertype') == 'bod' ? 'selected' : '' }}>BOD</option>
          </select>
          @error('usertype')
          <p class="text-sm text-danger mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div class="mb-4">
          <label for="password" class="label">Password</label>
          <input type="password" id="password" name="password" class="input-field @error('password') border-danger @enderror" placeholder="Masukkan password">
          @error('password')
          <p class="text-sm text-danger mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div class="mb-4">
          <label for="password_confirmation" class="label">Confirm Password</label>
          <input type="password" id="password_confirmation" name="password_confirmation" class="input-field @error('password_confirmation') border-danger @enderror" placeholder="Konfirmasi password">
          @error('password_confirmation')
          <p class="text-sm text-danger mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div class="mb-4">
          <label for="image" class="label">Foto Profil</label>
          <div class="relative">
            <input type="file" id="image" name="image" class="input-field file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 @error('image') border-danger @enderror">
          </div>
          @error('image')
          <p class="text-sm text-danger mt-1">{{ $message }}</p>
          @enderror
        </div>
      </div>
      <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
        <button type="submit" class="btn-primary">
          <i data-lucide="save" class="w-4 h-4"></i>
          Submit
        </button>
        <a href="{{ route('rootsuperuser/products') }}" class="btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>

@endsection
