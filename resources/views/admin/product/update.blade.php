@extends('layouts.applayout')
@section('content')
@include('components.admin-sidebar', ['activeMenu' => 'products'])

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="py-12">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card shadow-sm sm:rounded-lg">
                <div class="card-body text-gray-900">
                    <h1 class="mb-4">Update User</h1>
                    <hr />
                    @if (session()->has('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif
                    <p><a href="{{ route('admin/products') }}" class="btn btn-primary mb-4">Kembali</a></p>

                    <form action="{{ route('admin/products/update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <div class="mb-3">
                                <!-- Tampilkan gambar profil jika ada -->
                                @if ($user->image)
                                <img src="{{ asset('storage/' . $user->image) }}" alt="Profile Image" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                @else
                                <img src="{{ asset('storage/default-avatar.png') }}" alt="Default Image" class="img-thumbnail rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                @endif
                            </div>
                            <label for="image" class="form-label">Foto Profil</label>
                            <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror">
                            @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Masukkan nama" value="{{ $user->name }}">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Masukkan email" value="{{ $user->email }}">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan password">
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="usertype" class="form-label">User Type</label>
                            <select id="usertype" name="usertype" class="form-control @error('usertype') is-invalid @enderror">
                                <option value="">Select User Type</option>
                                <option value="operator" {{ $user->usertype == 'operator' ? 'selected' : '' }}>Operator</option>
                                <option value="bod" {{ $user->usertype == 'bod' ? 'selected' : '' }}>BOD</option>
                            </select>
                            @error('usertype')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->

    <div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->
@endsection