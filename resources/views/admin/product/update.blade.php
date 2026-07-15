@extends('layouts.applayout')
@section('title', 'User - Edit')
@section('content')

<x-dashboard.page-header
  title="Update User"
  description="Sunting data pengguna"
  :actions="'<a href=\"' . route('admin/products') . '\" class=\"btn-secondary\"><i data-lucide=\"arrow-left\" class=\"w-4 h-4\"></i> Kembali</a>'"
/>

<div class="card">
  <div class="card-header">
    <h3 class="text-base font-semibold text-gray-900">Informasi Pengguna</h3>
    <p class="text-sm text-gray-500">Perbarui data pengguna yang diperlukan.</p>
  </div>
  <div class="card-body">
    <form action="{{ route('admin/products/update', $user->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="mb-4 md:col-span-2">
          <label class="label">Foto Profil Saat Ini</label>
          <div class="flex items-center gap-4">
            @if ($user->image)
            <img src="{{ asset('storage/' . $user->image) }}" alt="Profile Image" class="rounded-full object-cover" style="width: 100px; height: 100px;">
            @else
            <div class="w-[100px] h-[100px] rounded-full bg-gray-100 flex items-center justify-center">
              <i data-lucide="user" class="w-8 h-8 text-gray-400"></i>
            </div>
            @endif
            <div class="text-sm text-gray-500">
              <p>Biarkan kosong jika tidak ingin mengubah foto.</p>
            </div>
          </div>
        </div>
        <div class="mb-4 md:col-span-2">
          <label for="image" class="label">Ganti Foto Profil</label>
          <div class="relative">
            <input type="file" id="image" name="image" class="input-field file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 @error('image') border-danger @enderror">
          </div>
          @error('image')
          <p class="text-sm text-danger mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div class="mb-4">
          <label for="name" class="label">Nama</label>
          <input type="text" id="name" name="name" class="input-field @error('name') border-danger @enderror" placeholder="Masukkan nama" value="{{ $user->name }}">
          @error('name')
          <p class="text-sm text-danger mt-1">{{ $message }}</p>
          @enderror
        </div>
        <div class="mb-4">
          <label for="email" class="label">Email</label>
          <input type="email" id="email" name="email" class="input-field @error('email') border-danger @enderror" placeholder="Masukkan email" value="{{ $user->email }}">
          @error('email')
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
          <label for="usertype" class="label">User Type</label>
          <select id="usertype" name="usertype" class="select-field @error('usertype') border-danger @enderror">
            <option value="">Select User Type</option>
            <option value="operator" {{ $user->usertype == 'operator' ? 'selected' : '' }}>Operator</option>
            <option value="bod" {{ $user->usertype == 'bod' ? 'selected' : '' }}>BOD</option>
          </select>
          @error('usertype')
          <p class="text-sm text-danger mt-1">{{ $message }}</p>
          @enderror
        </div>
      </div>
      <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
        <button type="submit" class="btn-primary">
          <i data-lucide="save" class="w-4 h-4"></i>
          Update
        </button>
        <a href="{{ route('admin/products') }}" class="btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>

@endsection
