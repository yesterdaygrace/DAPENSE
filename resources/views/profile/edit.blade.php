@php
$prefix = Auth::user()->usertype === 'bod' ? 'bod' : (Auth::user()->usertype === 'operator' ? 'operator' : 'admin');
@endphp

@extends('layouts.applayout')
@section('content')
@include('components.admin-sidebar', ['activeMenu' => 'dashboard'])

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="shadow-sm card">
            <div class="text-gray-900 card-body">
                <h2 class="mb-4 fw-semibold">Update profile</h2>
                <hr />

                @if (session('status') === 'profile-updated')
                <div class="alert alert-success">{{ __('Your profile has been updated successfully.') }}</div>
                @endif

                @if (session()->has('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <a href="{{ route($prefix . '/dashboard') }}" class="mb-4 btn btn-primary">&larr; Kembali</a>

                <form action="{{ route('profile.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        @if ($user->image)
                        <img src="{{ asset('storage/' . $user->image) }}" alt="Profile Image" class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                        @else
                        <img src="{{ asset('storage/default-avatar.png') }}" alt="Default Image" class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Foto profil</label>
                        <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror">
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ $user->name }}">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ $user->email }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="content-backdrop fade"></div>
</div>
@endsection
