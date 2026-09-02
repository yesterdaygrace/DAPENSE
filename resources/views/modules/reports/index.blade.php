@extends('layouts.applayout')

@section('title', 'Laporan')

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
        <span class="text-gray-900 font-medium">Laporan</span>
    </nav>

    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Laporan</h1>
        <p class="mt-2 text-gray-600">Hasilkan laporan keuangan, buku besar, dan neraca saldo</p>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Laporan Dibuat</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">342</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-blue-50 flex items-center justify-center">
                    <i data-lucide='file' class='text-2xl text-blue-600' aria-hidden="true"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm">
                <span class="text-green-600">↑ 28</span>
                <span class="text-gray-500">bulan ini</span>
            </div>
        </div>

        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Periode Laporan</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">Jul 2026</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-green-50 flex items-center justify-center">
                    <i data-lucide='calendar' class='text-2xl text-green-600' aria-hidden="true"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm">
                <span class="text-gray-600">Periode saat ini</span>
            </div>
        </div>

        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Neraca Saldo</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">Balanced</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-green-50 flex items-center justify-center">
                    <i data-lucide='check-circle' class='text-2xl text-green-600' aria-hidden="true"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm">
                <span class="text-green-600">✓</span>
                <span class="text-gray-500">Tidak ada kesalahan</span>
            </div>
        </div>

        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Ekspor</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">89</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-purple-50 flex items-center justify-center">
                    <i data-lucide='download' class='text-2xl text-purple-600' aria-hidden="true"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm">
                <span class="text-gray-500">PDF & Excel</span>
            </div>
        </div>
    </div>

    <!-- Feature Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Journal Summary -->
        <a href="{{ route('jurnaling-list') }}" class="bg-white rounded-[--radius-card] p-6 shadow-card hover:shadow-card-hover transition-shadow cursor-pointer group">
            <div class="w-12 h-12 rounded-[--radius-button] bg-primary-50 flex items-center justify-center text-primary mb-4 group-hover:scale-110 transition-transform">
                <i data-lucide='receipt' class='text-2xl' aria-hidden="true"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Ringkasan Jurnal</h3>
            <p class="text-sm text-gray-600 mb-4">Lihat ringkasan entri jurnal per periode</p>
            <div class="flex items-center text-primary text-sm font-medium">
                <span>Buka</span>
                <i data-lucide='arrow-right' class='ml-1 group-hover:translate-x-1 transition-transform' aria-hidden="true"></i>
            </div>
        </a>

        <!-- General Ledger -->
        <a href="{{ route('bukubesar') }}" class="bg-white rounded-[--radius-card] p-6 shadow-card hover:shadow-card-hover transition-shadow cursor-pointer group">
            <div class="w-12 h-12 rounded-[--radius-button] bg-primary-50 flex items-center justify-center text-primary mb-4 group-hover:scale-110 transition-transform">
                <i data-lucide='book' class='text-2xl' aria-hidden="true"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Buku Besar</h3>
            <p class="text-sm text-gray-600 mb-4">Lihat pergerakan dan saldo akun</p>
            <div class="flex items-center text-primary text-sm font-medium">
                <span>Buka</span>
                <i data-lucide='arrow-right' class='ml-1 group-hover:translate-x-1 transition-transform' aria-hidden="true"></i>
            </div>
        </a>

        <!-- Trial Balance -->
        <a href="{{ route('neraca-saldo') }}" class="bg-white rounded-[--radius-card] p-6 shadow-card hover:shadow-card-hover transition-shadow cursor-pointer group">
            <div class="w-12 h-12 rounded-[--radius-button] bg-primary-50 flex items-center justify-center text-primary mb-4 group-hover:scale-110 transition-transform">
                <i data-lucide='calculator' class='text-2xl' aria-hidden="true"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Neraca Saldo</h3>
            <p class="text-sm text-gray-600 mb-4">Verifikasi saldo debit dan kredit</p>
            <div class="flex items-center text-primary text-sm font-medium">
                <span>Buka</span>
                <i data-lucide='arrow-right' class='ml-1 group-hover:translate-x-1 transition-transform' aria-hidden="true"></i>
            </div>
        </a>

    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h2>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('neraca-saldo') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-[--radius-button] hover:bg-primary-600 transition-colors text-sm font-medium">
                <i class='calculator'></i>
                <span>Hitung Neraca Saldo</span>
            </a>
            <a href="{{ route('bukubesar') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-[--radius-button] hover:bg-gray-200 transition-colors text-sm font-medium">
                <i class='book-open'></i>
                <span>Lihat Buku Besar</span>
            </a>
            <a href="{{ route('jurnaling-list') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-[--radius-button] hover:bg-gray-200 transition-colors text-sm font-medium">
                <i class='receipt'></i>
                <span>Ringkasan Jurnal</span>
            </a>
        </div>
    </div>

    <!-- Report Types Info -->
    <div class="bg-green-50 rounded-[--radius-card] p-6 border border-green-100">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-[--radius-button] bg-green-100 flex items-center justify-center flex-shrink-0">
                <i data-lucide='download' class='text-xl text-green-600' aria-hidden="true"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 mb-2">Opsi Ekspor</h3>
                <p class="text-sm text-gray-700">Semua laporan dapat diekspor ke format Excel atau PDF. Pilih format yang diinginkan dari halaman laporan.</p>
            </div>
        </div>
    </div>
</div>
@endsection
