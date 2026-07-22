@extends('layouts.applayout')

@section('title', 'Transaksi')

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
        <span class="text-gray-900 font-medium">Transaksi</span>
    </nav>

    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Transaksi</h1>
        <p class="mt-2 text-gray-600">Catat entri jurnal, kelola draft, dan proses transaksi</p>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Jurnal</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">1,284</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-blue-50 flex items-center justify-center">
                    <i class='bx bx-list-ul text-2xl text-blue-600'></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm">
                <span class="text-green-600">↑ 48</span>
                <span class="text-gray-500">bulan ini</span>
            </div>
        </div>

        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Draft Jurnal</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">12</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-amber-50 flex items-center justify-center">
                    <i class='bx bx-file-blank text-2xl text-amber-600'></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm">
                <span class="text-gray-600">Tertunda</span>
            </div>
        </div>

        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">156</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-green-50 flex items-center justify-center">
                    <i class='bx bx-calendar text-2xl text-green-600'></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm">
                <span class="text-green-600">↑ 24%</span>
                <span class="text-gray-500">vs bulan lalu</span>
            </div>
        </div>

        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Template</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">8</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-purple-50 flex items-center justify-center">
                    <i class='bx bx-copy text-2xl text-purple-600'></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm">
                <span class="text-gray-500">Tersedia</span>
            </div>
        </div>
    </div>

    <!-- Feature Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Journal Entry -->
        <a href="{{ $prefix }}/transactions/journal-entry" class="bg-white rounded-[--radius-card] p-6 shadow-card hover:shadow-card-hover transition-shadow cursor-pointer group">
            <div class="w-12 h-12 rounded-[--radius-button] bg-primary-50 flex items-center justify-center text-primary mb-4 group-hover:scale-110 transition-transform">
                <i class='bx bx-edit text-2xl'></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Entri Jurnal</h3>
            <p class="text-sm text-gray-600 mb-4">Buat entri jurnal baru dan catat transaksi</p>
            <div class="flex items-center text-primary text-sm font-medium">
                <span>Buka</span>
                <i class='bx bx-right-arrow-alt ml-1 group-hover:translate-x-1 transition-transform'></i>
            </div>
        </a>

        <!-- Journal List -->
        <a href="{{ $prefix }}/jurnaling/showing" class="bg-white rounded-[--radius-card] p-6 shadow-card hover:shadow-card-hover transition-shadow cursor-pointer group">
            <div class="w-12 h-12 rounded-[--radius-button] bg-primary-50 flex items-center justify-center text-primary mb-4 group-hover:scale-110 transition-transform">
                <i class='bx bx-list-ul text-2xl'></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Daftar Jurnal</h3>
            <p class="text-sm text-gray-600 mb-4">Lihat dan cari semua entri jurnal yang sudah diposting</p>
            <div class="flex items-center text-primary text-sm font-medium">
                <span>Buka</span>
                <i class='bx bx-right-arrow-alt ml-1 group-hover:translate-x-1 transition-transform'></i>
            </div>
        </a>



        <!-- Export -->
        <a href="{{ $prefix }}/jurnaling/export" class="bg-white rounded-[--radius-card] p-6 shadow-card hover:shadow-card-hover transition-shadow cursor-pointer group">
            <div class="w-12 h-12 rounded-[--radius-button] bg-primary-50 flex items-center justify-center text-primary mb-4 group-hover:scale-110 transition-transform">
                <i class='bx bx-export text-2xl'></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Ekspor</h3>
            <p class="text-sm text-gray-600 mb-4">Unduh entri jurnal ke Excel atau PDF</p>
            <div class="flex items-center text-primary text-sm font-medium">
                <span>Buka</span>
                <i class='bx bx-right-arrow-alt ml-1 group-hover:translate-x-1 transition-transform'></i>
            </div>
        </a>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h2>
        <div class="flex flex-wrap gap-3">
            <a href="{{ $prefix }}/transactions/journal-entry" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-[--radius-button] hover:bg-primary-600 transition-colors text-sm font-medium">
                <i class='bx bx-plus'></i>
                <span>Entri Jurnal Baru</span>
            </a>
            <a href="{{ $prefix }}/jurnaling/showing" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-[--radius-button] hover:bg-gray-200 transition-colors text-sm font-medium">
                <i class='bx bx-search'></i>
                <span>Cari Jurnal</span>
            </a>
            <a href="{{ $prefix }}/jurnaling/export" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-[--radius-button] hover:bg-gray-200 transition-colors text-sm font-medium">
                <i class='bx bx-download'></i>
                <span>Ekspor Data</span>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Transaksi Terbaru</h2>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-[--radius-button]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[--radius-button] bg-blue-100 flex items-center justify-center">
                        <i class='bx bx-receipt text-blue-600'></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">JV-2024-0789</p>
                        <p class="text-xs text-gray-500">Transfer Bank - Diposting</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-900">Rp 15,000,000</p>
                    <p class="text-xs text-gray-500">2 jam lalu</p>
                </div>
            </div>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-[--radius-button]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[--radius-button] bg-green-100 flex items-center justify-center">
                        <i class='bx bx-receipt text-green-600'></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">JV-2024-0788</p>
                        <p class="text-xs text-gray-500">Pendapatan Penjualan - Diposting</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-900">Rp 28,500,000</p>
                    <p class="text-xs text-gray-500">5 jam lalu</p>
                </div>
            </div>
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-[--radius-button]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-[--radius-button] bg-purple-100 flex items-center justify-center">
                        <i class='bx bx-receipt text-purple-600'></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">JV-2024-0787</p>
                        <p class="text-xs text-gray-500">Beban Operasional - Diposting</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-900">Rp 3,200,000</p>
                    <p class="text-xs text-gray-500">1 hari lalu</p>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ $prefix }}/jurnaling/showing" class="text-sm text-primary hover:text-primary-600 font-medium">
                Lihat semua transaksi →
            </a>
        </div>
    </div>
</div>
@endsection
