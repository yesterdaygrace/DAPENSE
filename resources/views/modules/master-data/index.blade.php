@extends('layouts.applayout')

@section('title', 'Data Master')

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
        <span class="text-gray-900 font-medium">Data Master</span>
    </nav>

    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Data Master</h1>
        <p class="mt-2 text-gray-600">Kelola periode akuntansi, kode akun, dan saldo awal</p>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Akun</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">245</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-blue-50 flex items-center justify-center">
                    <i class='bx bx-spreadsheet text-2xl text-blue-600'></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm">
                <span class="text-green-600">↑ 12</span>
                <span class="text-gray-500">bulan ini</span>
            </div>
        </div>

        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Periode Aktif</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">2024</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-green-50 flex items-center justify-center">
                    <i class='bx bx-calendar text-2xl text-green-600'></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm">
                <span class="text-gray-600">Periode 07</span>
            </div>
        </div>

        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Header Akun</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">28</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-purple-50 flex items-center justify-center">
                    <i class='bx bx-table text-2xl text-purple-600'></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm">
                <span class="text-gray-500">Dikonfigurasi</span>
            </div>
        </div>

        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Saldo Awal</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">Set</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-amber-50 flex items-center justify-center">
                    <i class='bx bx-money text-2xl text-amber-600'></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1 text-sm">
                <span class="text-green-600">✓</span>
                <span class="text-gray-500">Seimbang</span>
            </div>
        </div>
    </div>

    <!-- Feature Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Accounting Period -->
        <a href="{{ $prefix }}/periodes" class="bg-white rounded-[--radius-card] p-6 shadow-card hover:shadow-card-hover transition-shadow cursor-pointer group">
            <div class="w-12 h-12 rounded-[--radius-button] bg-primary-50 flex items-center justify-center text-primary mb-4 group-hover:scale-110 transition-transform">
                <i class='bx bx-calendar text-2xl'></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Periode Akuntansi</h3>
            <p class="text-sm text-gray-600 mb-4">Kelola tahun fiskal dan periode akuntansi</p>
            <div class="flex items-center text-primary text-sm font-medium">
                <span>Buka</span>
                <i class='bx bx-right-arrow-alt ml-1 group-hover:translate-x-1 transition-transform'></i>
            </div>
        </a>

        <!-- Chart of Accounts -->
        <a href="{{ $prefix }}/master-data/coa-workspace" class="bg-white rounded-[--radius-card] p-6 shadow-card hover:shadow-card-hover transition-shadow cursor-pointer group">
            <div class="w-12 h-12 rounded-[--radius-button] bg-primary-50 flex items-center justify-center text-primary mb-4 group-hover:scale-110 transition-transform">
                <i class='bx bx-spreadsheet text-2xl'></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Kode Akun</h3>
            <p class="text-sm text-gray-600 mb-4">Lihat dan kelola struktur akun</p>
            <div class="flex items-center text-primary text-sm font-medium">
                <span>Buka</span>
                <i class='bx bx-right-arrow-alt ml-1 group-hover:translate-x-1 transition-transform'></i>
            </div>
        </a>

        <!-- Account Headers -->
        <a href="{{ $prefix }}/master-data/coa-workspace?tab=headers" class="bg-white rounded-[--radius-card] p-6 shadow-card hover:shadow-card-hover transition-shadow cursor-pointer group">
            <div class="w-12 h-12 rounded-[--radius-button] bg-primary-50 flex items-center justify-center text-primary mb-4 group-hover:scale-110 transition-transform">
                <i class='bx bx-table text-2xl'></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Header Akun</h3>
            <p class="text-sm text-gray-600 mb-4">Konfigurasi kategori dan pengelompokan akun</p>
            <div class="flex items-center text-primary text-sm font-medium">
                <span>Buka</span>
                <i class='bx bx-right-arrow-alt ml-1 group-hover:translate-x-1 transition-transform'></i>
            </div>
        </a>

        <!-- Opening Balance -->
        <a href="{{ $prefix }}/saldoawal" class="bg-white rounded-[--radius-card] p-6 shadow-card hover:shadow-card-hover transition-shadow cursor-pointer group">
            <div class="w-12 h-12 rounded-[--radius-button] bg-primary-50 flex items-center justify-center text-primary mb-4 group-hover:scale-110 transition-transform">
                <i class='bx bx-money text-2xl'></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Saldo Awal</h3>
            <p class="text-sm text-gray-600 mb-4">Atur saldo awal akun</p>
            <div class="flex items-center text-primary text-sm font-medium">
                <span>Buka</span>
                <i class='bx bx-right-arrow-alt ml-1 group-hover:translate-x-1 transition-transform'></i>
            </div>
        </a>

        <!-- Import Data -->
        <a href="{{ $prefix }}/master-data/coa-workspace?tab=import" class="bg-white rounded-[--radius-card] p-6 shadow-card hover:shadow-card-hover transition-shadow cursor-pointer group">
            <div class="w-12 h-12 rounded-[--radius-button] bg-primary-50 flex items-center justify-center text-primary mb-4 group-hover:scale-110 transition-transform">
                <i class='bx bx-import text-2xl'></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Impor Data</h3>
            <p class="text-sm text-gray-600 mb-4">Impor akun secara massal dari Excel atau CSV</p>
            <div class="flex items-center text-primary text-sm font-medium">
                <span>Buka</span>
                <i class='bx bx-right-arrow-alt ml-1 group-hover:translate-x-1 transition-transform'></i>
            </div>
        </a>

        <!-- Export Data -->
        <a href="{{ $prefix }}/master-data/coa-workspace?tab=export" class="bg-white rounded-[--radius-card] p-6 shadow-card hover:shadow-card-hover transition-shadow cursor-pointer group">
            <div class="w-12 h-12 rounded-[--radius-button] bg-primary-50 flex items-center justify-center text-primary mb-4 group-hover:scale-110 transition-transform">
                <i class='bx bx-export text-2xl'></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Ekspor Data</h3>
            <p class="text-sm text-gray-600 mb-4">Unduh kode akun untuk cadangan</p>
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
            <a href="{{ $prefix }}/master-data/coa-workspace?action=new" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-[--radius-button] hover:bg-primary-600 transition-colors text-sm font-medium">
                <i class='bx bx-plus'></i>
                <span>Akun Baru</span>
            </a>
            <a href="{{ $prefix }}/master-data/coa-workspace?tab=import" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-[--radius-button] hover:bg-gray-200 transition-colors text-sm font-medium">
                <i class='bx bx-import'></i>
                <span>Impor Akun</span>
            </a>
            <a href="{{ $prefix }}/periodes?action=new" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-[--radius-button] hover:bg-gray-200 transition-colors text-sm font-medium">
                <i class='bx bx-calendar-plus'></i>
                <span>Periode Baru</span>
            </a>
        </div>
    </div>
</div>
@endsection
