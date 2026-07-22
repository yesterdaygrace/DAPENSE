@extends('layouts.applayout')

@section('title', 'Administrasi')

@section('content')
@php
    $prefix = match(Auth::user()->usertype) {
        'rootsuperuser' => '/rootsuperuser',
        'admin' => '/admin',
        'operator' => '/operator',
        'bod' => '/bod',
        default => ''
    };
@endphp

<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-600">
        <a href="{{ $prefix }}/dashboard" class="hover:text-primary transition-colors">Dasbor</a>
        <i class='bx bx-chevron-right text-gray-400'></i>
        <span class="text-gray-900 font-medium">Administrasi</span>
    </nav>

    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Administrasi</h1>
        <p class="mt-2 text-gray-600">Kelola pengguna, peran, izin, dan log audit sistem</p>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Pengguna</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">24</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-blue-50 flex items-center justify-center">
                    <i class='bx bx-user text-2xl text-blue-600'></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm">
                <span class="text-green-600">↑ 3</span>
                <span class="text-gray-500">bulan ini</span>
            </div>
        </div>

        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Sesi Aktif</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">18</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-green-50 flex items-center justify-center">
                    <i class='bx bx-check-circle text-2xl text-green-600'></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm">
                <span class="text-gray-600">Sedang online</span>
            </div>
        </div>

        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Peran Pengguna</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">5</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-purple-50 flex items-center justify-center">
                    <i class='bx bx-shield-quarter text-2xl text-purple-600'></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm">
                <span class="text-gray-500">Dikonfigurasi</span>
            </div>
        </div>

        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Entri Audit</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">3,428</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-amber-50 flex items-center justify-center">
                    <i class='bx bx-history text-2xl text-amber-600'></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm">
                <span class="text-gray-500">Total tercatat</span>
            </div>
        </div>
    </div>

    <!-- Feature Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <!-- User Management -->
        <a href="{{ $prefix }}/products" class="bg-white rounded-[--radius-card] p-6 shadow-card hover:shadow-card-hover transition-shadow cursor-pointer group">
            <div class="w-12 h-12 rounded-[--radius-button] bg-primary-50 flex items-center justify-center text-primary mb-4 group-hover:scale-110 transition-transform">
                <i class='bx bx-user text-2xl'></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Manajemen Pengguna</h3>
            <p class="text-sm text-gray-600 mb-4">Tambah, edit, dan kelola akun serta akses pengguna</p>
            <div class="flex items-center text-primary text-sm font-medium">
                <span>Buka</span>
                <i class='bx bx-right-arrow-alt ml-1 group-hover:translate-x-1 transition-transform'></i>
            </div>
        </a>

        <!-- Quick Actions -->
        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h2>
            <div class="flex flex-wrap gap-3">
                <a href="{{ $prefix }}/products?action=new" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-[--radius-button] hover:bg-primary-600 transition-colors text-sm font-medium">
                    <i class='bx bx-plus'></i>
                    <span>Tambah Pengguna Baru</span>
                </a>
                <a href="{{ $prefix }}/products" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-[--radius-button] hover:bg-gray-200 transition-colors text-sm font-medium">
                    <i class='bx bx-search'></i>
                    <span>Cari Pengguna</span>
                </a>
            </div>
        </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Aktivitas Pengguna Terbaru</h2>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-[--radius-button]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[--radius-button] bg-blue-100 flex items-center justify-center">
                        <i class='bx bx-user-plus text-blue-600'></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Pengguna baru terdaftar</p>
                        <p class="text-xs text-gray-500">john.doe@example.com</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500">2 jam lalu</p>
                </div>
            </div>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-[--radius-button]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[--radius-button] bg-green-100 flex items-center justify-center">
                        <i class='bx bx-log-in text-green-600'></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Pengguna login</p>
                        <p class="text-xs text-gray-500">admin@example.com</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500">3 jam lalu</p>
                </div>
            </div>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-[--radius-button]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[--radius-button] bg-purple-100 flex items-center justify-center">
                        <i class='bx bx-shield text-purple-600'></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Peran diperbarui</p>
                        <p class="text-xs text-gray-500">Izin peran akuntan dimodifikasi</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500">1 hari lalu</p>
                </div>
            </div>
        </div>
    </div>

    </div>
</div>
@endsection
