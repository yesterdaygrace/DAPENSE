@php
$usertype = Auth::user()->usertype;
$prefix = $usertype === 'rootsuperuser' ? 'rootsuperuser' : ($usertype === 'bod' ? 'bod' : ($usertype === 'operator' ? 'operator' : 'admin'));
@endphp

@extends('layouts.applayout')
@section('title', 'Profil')
@section('content')

<x-dashboard.page-header title="Profil Saya" description="Kelola informasi profil dan pengaturan akun Anda" />

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Profile Image Card --}}
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-bold text-gray-900">Foto Profil</h3>
        </div>
        <div class="card-body flex flex-col items-center">
            <div class="mb-4">
                @if ($user->image)
                <img src="{{ asset('storage/' . $user->image) }}" alt="Profile Image" class="w-28 h-28 rounded-full border-4 border-gray-100 object-cover shadow-sm">
                @else
                <img src="{{ asset('storage/default-avatar.png') }}" alt="Default Image" class="w-28 h-28 rounded-full border-4 border-gray-100 object-cover shadow-sm">
                @endif
            </div>
            <div class="text-center">
                <p class="text-sm font-bold text-gray-900">{{ $user->name }}</p>
                <p class="text-xs text-gray-500 mt-0.5">{{ $user->email }}</p>
            </div>
            <div class="mt-3">
                <span class="badge badge-primary">{{ ucfirst($usertype) }}</span>
            </div>
        </div>
    </div>

    {{-- Edit Profile Card --}}
    <div class="lg:col-span-2">
        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-bold text-gray-900">Informasi Profil</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <label for="image" class="label">Foto Profil</label>
                        <input type="file" id="image" name="image" accept="image/*" class="input-field @error('image') border-danger @enderror">
                        @error('image')
                        <p class="text-xs text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="name" class="label">Nama</label>
                            <input type="text" id="name" name="name" class="input-field @error('name') border-danger @enderror" value="{{ $user->name }}" required autofocus autocomplete="name">
                            @error('name')
                            <p class="text-xs text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="label">Email</label>
                            <input type="email" id="email" name="email" class="input-field @error('email') border-danger @enderror" value="{{ $user->email }}" required autocomplete="username">
                            @error('email')
                            <p class="text-xs text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="password" class="label">Password Baru (Opsional)</label>
                        <input type="password" id="password" name="password" class="input-field @error('password') border-danger @enderror" autocomplete="new-password">
                        @error('password')
                        <p class="text-xs text-danger mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" class="btn-primary">Simpan Perubahan</button>
                        <a href="{{ route($prefix . '/dashboard') }}" class="btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
