@extends('layouts.applayout')

@section('title', 'Pengaturan')

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
        <span class="text-gray-900 font-medium">Pengaturan</span>
    </nav>

    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Pengaturan</h1>
        <p class="mt-2 text-gray-600">Konfigurasi preferensi sistem, basis data, keamanan, dan pengaturan email</p>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Status Sistem</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">Online</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-green-50 flex items-center justify-center">
                    <i class='bx bx-check-circle text-2xl text-green-600'></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm">
                <span class="text-green-600">✓</span>
                <span class="text-gray-500">Semua sistem beroperasi</span>
            </div>
        </div>

        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Basis Data</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">Connected</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-blue-50 flex items-center justify-center">
                    <i class='bx bx-data text-2xl text-blue-600'></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm">
                <span class="text-gray-600">MySQL 8.0</span>
            </div>
        </div>

        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Keamanan</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">Enabled</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-purple-50 flex items-center justify-center">
                    <i class='bx bx-lock text-2xl text-purple-600'></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm">
                <span class="text-green-600">✓</span>
                <span class="text-gray-500">SSL Aktif</span>
            </div>
        </div>

        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Layanan Email</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">Active</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-amber-50 flex items-center justify-center">
                    <i class='bx bx-envelope text-2xl text-amber-600'></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm">
                <span class="text-gray-600">SMTP Dikonfigurasi</span>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Sistem</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-[--radius-button]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[--radius-button] bg-blue-100 flex items-center justify-center">
                        <i class='bx bx-code-alt text-blue-600'></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Versi Aplikasi</p>
                        <p class="text-sm font-medium text-gray-900">v1.0.0</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-[--radius-button]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[--radius-button] bg-green-100 flex items-center justify-center">
                        <i class='bx bx-server text-green-600'></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Versi PHP</p>
                        <p class="text-sm font-medium text-gray-900">8.2.0</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-[--radius-button]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[--radius-button] bg-purple-100 flex items-center justify-center">
                        <i class='bx bx-package text-purple-600'></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Versi Laravel</p>
                        <p class="text-sm font-medium text-gray-900">11.x</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-[--radius-button]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[--radius-button] bg-amber-100 flex items-center justify-center">
                        <i class='bx bx-time text-amber-600'></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Zona Waktu</p>
                        <p class="text-sm font-medium text-gray-900">Asia/Jakarta</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Tautan Cepat</h2>
        <div class="flex flex-wrap gap-3">
            <a href="{{ $prefix }}/dashboard" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-[--radius-button] hover:bg-primary-600 transition-colors text-sm font-medium">
                <i class='bx bx-home'></i>
                <span>Kembali ke Dasbor</span>
            </a>
            <a href="{{ $prefix }}/products" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-[--radius-button] hover:bg-gray-200 transition-colors text-sm font-medium">
                <i class='bx bx-user'></i>
                <span>Kelola Pengguna</span>
            </a>
        </div>
    </div>
</div>
@endsection
