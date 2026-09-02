@extends('layouts.applayout')

@section('title', 'Keuangan')

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
        <a href="{{ route('dashboard') }}" class="hover:text-primary transition-colors">Dasbor</a>
        <i data-lucide='chevron-right' class='text-gray-400' aria-hidden="true"></i>
        <span class="text-gray-900 font-medium">Keuangan</span>
    </nav>

    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Keuangan</h1>
        <p class="mt-2 text-gray-600">Kelola rekonsiliasi bank, anggaran, penutupan fiskal, dan laporan keuangan</p>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Rekening Bank</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">8</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-blue-50 flex items-center justify-center">
                    <i data-lucide='landmark' class='text-2xl text-blue-600' aria-hidden="true"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm">
                <span class="text-green-600">✓</span>
                <span class="text-gray-500">Semua sudah direkonsiliasi</span>
            </div>
        </div>

        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Status Anggaran</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">78%</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-green-50 flex items-center justify-center">
                    <i data-lucide='dollar-sign' class='text-2xl text-green-600' aria-hidden="true"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm">
                <span class="text-gray-600">Tergunakan</span>
            </div>
        </div>

        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Periode Fiskal</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">Open</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-amber-50 flex items-center justify-center">
                    <i data-lucide='calendar-check' class='text-2xl text-amber-600' aria-hidden="true"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm">
                <span class="text-gray-600">2024 - Period 07</span>
            </div>
        </div>

        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Laporan</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">142</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-purple-50 flex items-center justify-center">
                    <i data-lucide='file' class='text-2xl text-purple-600' aria-hidden="true"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm">
                <span class="text-gray-500">Generated</span>
            </div>
        </div>
    </div>

    <!-- Available Actions -->
    <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Aksi Tersedia</h2>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('neraca-saldo') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-[--radius-button] hover:bg-primary-600 transition-colors text-sm font-medium">
                <i class='calculator'></i>
                <span>Lihat Neraca Saldo</span>
            </a>
            <a href="{{ route('bukubesar') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-[--radius-button] hover:bg-gray-200 transition-colors text-sm font-medium">
                <i class='book-open'></i>
                <span>Lihat Buku Besar</span>
            </a>
            <a href="{{ route('jurnaling-list') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-[--radius-button] hover:bg-gray-200 transition-colors text-sm font-medium">
                <i class='receipt'></i>
                <span>Lihat Jurnal</span>
            </a>
        </div>
    </div>
</div>
@endsection
