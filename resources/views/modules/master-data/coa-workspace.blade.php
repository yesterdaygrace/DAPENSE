@extends('layouts.applayout')

@section('title', 'Ruang Kerja COA')

@section('content')
@php
    $prefix = match(Auth::user()->usertype) {
        'rootsuperuser' => '/rootsuperuser',
        'admin' => '/admin',
        'operator' => '/operator',
        'bod' => '/bod',
        default => ''
    };
    $routePrefix = Auth::user()->usertype;
@endphp

<div x-data="{ activeTab: '{{ request()->query('tab', 'accounts') }}' }">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-600 mb-6">
        <a href="{{ $prefix }}/dashboard" class="hover:text-primary transition-colors">Dasbor</a>
        <i class='bx bx-chevron-right text-gray-400'></i>
        <a href="{{ $prefix }}/master-data" class="hover:text-primary transition-colors">Data Master</a>
        <i class='bx bx-chevron-right text-gray-400'></i>
        <span class="text-gray-900 font-medium">Ruang Kerja COA</span>
    </nav>

    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Kode Akun</h1>
        <p class="mt-2 text-gray-600">Kelola struktur dan konfigurasi kode akun</p>
    </div>

    <!-- Tab Navigation -->
    <div class="flex border-b border-gray-200 mb-6 overflow-x-auto">
        <button
            @click="activeTab = 'accounts'"
            :class="activeTab === 'accounts' ? 'border-primary text-primary' : 'text-gray-500 hover:text-gray-700'"
            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap"
        >
            Akun
        </button>
        <button
            @click="activeTab = 'headers'"
            :class="activeTab === 'headers' ? 'border-primary text-primary' : 'text-gray-500 hover:text-gray-700'"
            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap"
        >
            Header
        </button>
        <button
            @click="activeTab = 'header-mapping'"
            :class="activeTab === 'header-mapping' ? 'border-primary text-primary' : 'text-gray-500 hover:text-gray-700'"
            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap"
        >
            Pemetaan Header
        </button>
        <button
            @click="activeTab = 'import'"
            :class="activeTab === 'import' ? 'border-primary text-primary' : 'text-gray-500 hover:text-gray-700'"
            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap"
        >
            Impor
        </button>
        <button
            @click="activeTab = 'export'"
            :class="activeTab === 'export' ? 'border-primary text-primary' : 'text-gray-500 hover:text-gray-700'"
            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap"
        >
            Ekspor
        </button>
        <button
            @click="activeTab = 'audit-history'"
            :class="activeTab === 'audit-history' ? 'border-primary text-primary' : 'text-gray-500 hover:text-gray-700'"
            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap"
        >
            Riwayat Audit
        </button>
    </div>

    <!-- Accounts Tab -->
    <div x-show="activeTab === 'accounts'" class="space-y-6">
        <!-- Stats Row -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
                <p class="text-sm text-gray-600">Total Akun</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $coas->total() }}</p>
            </div>
            <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
                <p class="text-sm text-gray-600">Active</p>
                <p class="text-2xl font-bold text-green-600 mt-1">
                    {{ $coas->where('status', 'active')->count() }}
                </p>
            </div>
            <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
                <p class="text-sm text-gray-600">Inactive</p>
                <p class="text-2xl font-bold text-gray-400 mt-1">
                    {{ $coas->where('status', 'inactive')->count() }}
                </p>
            </div>
            <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
                <p class="text-sm text-gray-600">Headers</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $headers->total() }}</p>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="bg-white rounded-[--radius-card] shadow-card p-6">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <!-- Search -->
                <div class="relative flex-1 max-w-md">
                    <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400'></i>
                    <input type="text" placeholder="Cari akun..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-[--radius-button] focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                
                <!-- Filter -->
                <div class="relative">
                    <select class="appearance-none pl-4 pr-10 py-2 border border-gray-300 rounded-[--radius-button] focus:ring-2 focus:ring-primary focus:border-primary min-w-[160px]">
                        <option value="">Semua Kategori</option>
                        <option value="asset">Aset</option>
                        <option value="liability">Kewajiban</option>
                        <option value="equity">Ekuitas</option>
                        <option value="revenue">Pendapatan</option>
                        <option value="expense">Beban</option>
                    </select>
                    <i class='bx bx-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400'></i>
                </div>
                
                <!-- Create Button -->
                <a href="{{ route($routePrefix . '/account/coa/create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-[--radius-button] hover:bg-primary-600 transition-colors text-sm font-medium">
                    <i class='bx bx-plus'></i>
                    <span>Buat Akun</span>
                </a>
                <button class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-[--radius-button] hover:bg-gray-50 transition-colors text-sm font-medium">
                    <i class='bx bx-filter'></i>
                    <span>Filter</span>
                </button>
            </div>

            <!-- Accounts Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-gray-700 uppercase">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Kode</th>
                            <th class="px-4 py-3 font-semibold">Nama</th>
                            <th class="px-4 py-3 font-semibold">Kategori</th>
                            <th class="px-4 py-3 font-semibold">Saldo Normal</th>
                            <th class="px-4 py-3 font-semibold">Header</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                            <th class="px-4 py-3 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($coas as $coa)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $coa->kode_akun }}</td>
                            <td class="px-4 py-3">{{ $coa->nama_akun }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ ucfirst($coa->kategori) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 capitalize">{{ $coa->saldo_normal }}</td>
                            <td class="px-4 py-3">{{ $coa->headerCoa->nama_header ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if($coa->status ?? 'active' === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route($routePrefix . '/account/coa/edit', $coa->id) }}" class="text-primary hover:text-primary-600 inline-block">
                                    <i class='bx bx-edit'></i>
                                </a>
                                <a href="{{ route($routePrefix . '/account/coa/delete', $coa->id) }}" class="text-danger hover:text-danger-600 inline-block" onclick="return confirm('Apakah Anda yakin ingin menghapus akun ini?')">
                                    <i class='bx bx-trash'></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                Belum ada data akun
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Showing {{ $coas->firstItem() ?? 0 }} to {{ $coas->lastItem() ?? 0 }} of {{ $coas->total() }} entri
                </div>
                <div class="flex gap-2">
                    @if($coas->onFirstPage())
                        <button class="px-3 py-1 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed">Sebelumnya</button>
                    @else
                        <button class="px-3 py-1 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Sebelumnya</button>
                    @endif
                    
                    @for($i = 1; $i <= $coas->lastPage(); $i++)
                        @if($i == $coas->currentPage())
                            <button class="px-3 py-1 bg-primary text-white rounded-lg">{{ $i }}</button>
                        @else
                            <button class="px-3 py-1 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">{{ $i }}</button>
                        @endif
                    @endfor
                    
                    @if($coas->hasMorePages())
                        <button class="px-3 py-1 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Selanjutnya</button>
                    @else
                        <button class="px-3 py-1 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed">Selanjutnya</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Headers Tab -->
    <div x-show="activeTab === 'headers'" class="space-y-6">
        <!-- Stats Row -->
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
                <p class="text-sm text-gray-600">Total Header</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $headers->total() }}</p>
            </div>
            <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
                <p class="text-sm text-gray-600">Level 1</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $headers->where('level', 1)->count() }}</p>
            </div>
            <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
                <p class="text-sm text-gray-600">Level 2</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $headers->where('level', 2)->count() }}</p>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="bg-white rounded-[--radius-card] shadow-card p-6">
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <!-- Search -->
                <div class="relative flex-1 max-w-md">
                    <i class='bx bx-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400'></i>
                    <input type="text" placeholder="Cari header..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-[--radius-button] focus:ring-2 focus:ring-primary focus:border-primary">
                </div>
                
                <!-- Create Button -->
                <a href="{{ route($routePrefix . '/account/header/create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-[--radius-button] hover:bg-primary-600 transition-colors text-sm font-medium">
                    <i class='bx bx-plus'></i>
                    <span>Buat Header</span>
                </a>
            </div>

            <!-- Headers Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-gray-700 uppercase">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Kode</th>
                            <th class="px-4 py-3 font-semibold">Nama</th>
                            <th class="px-4 py-3 font-semibold">Level</th>
                            <th class="px-4 py-3 font-semibold">Induk</th>
                            <th class="px-4 py-3 font-semibold">Akun</th>
                            <th class="px-4 py-3 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($headers as $header)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $header->kode_header }}</td>
                            <td class="px-4 py-3">{{ $header->nama_header }}</td>
                            <td class="px-4 py-3">Level {{ $header->level }}</td>
                            <td class="px-4 py-3">{{ $header->parent_id ? $header->parent->nama_header : '—' }}</td>
                            <td class="px-4 py-3">{{ $header->coas_count ?? 0 }}</td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route($routePrefix . '/account/header/edit', $header->id) }}" class="text-primary hover:text-primary-600 inline-block">
                                    <i class='bx bx-edit'></i>
                                </a>
                                <a href="{{ route($routePrefix . '/account/header/delete', $header->id) }}" class="text-danger hover:text-danger-600 inline-block" onclick="return confirm('Apakah Anda yakin ingin menghapus header ini?')">
                                    <i class='bx bx-trash'></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                Belum ada data header
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    Showing {{ $headers->firstItem() ?? 0 }} to {{ $headers->lastItem() ?? 0 }} of {{ $headers->total() }} entri
                </div>
                <div class="flex gap-2">
                    @if($headers->onFirstPage())
                        <button class="px-3 py-1 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed">Sebelumnya</button>
                    @else
                        <button class="px-3 py-1 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Sebelumnya</button>
                    @endif
                    
                    @for($i = 1; $i <= $headers->lastPage(); $i++)
                        @if($i == $headers->currentPage())
                            <button class="px-3 py-1 bg-primary text-white rounded-lg">{{ $i }}</button>
                        @else
                            <button class="px-3 py-1 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">{{ $i }}</button>
                        @endif
                    @endfor
                    
                    @if($headers->hasMorePages())
                        <button class="px-3 py-1 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Selanjutnya</button>
                    @else
                        <button class="px-3 py-1 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed">Selanjutnya</button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Header Mapping Tab -->
    <div x-show="activeTab === 'header-mapping'" class="bg-white rounded-[--radius-card] shadow-card p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Pemetaan Header</h2>
            <p class="text-sm text-gray-600">Kelola penugasan akun COA ke header</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase">
                    <tr>
                            <th class="px-4 py-3 font-semibold">Nama Header</th>
                            <th class="px-4 py-3 font-semibold">Akun yang Ditugaskan</th>
                            <th class="px-4 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($headers as $header)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $header->nama_header }}</td>
                        <td class="px-4 py-3">
                            @php
                                $accounts = $header->coas->take(3);
                                $remaining = $header->coas->count() - 3;
                            @endphp
                            @foreach($accounts as $coa)
                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700 mr-1 mb-1">
                                    {{ $coa->kode_akun }}
                                </span>
                            @endforeach
                            @if($remaining > 0)
                                <span class="text-gray-500 text-xs">+{{ $remaining }} lainnya</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <button class="text-primary hover:text-primary-600 px-3 py-1 rounded-lg hover:bg-gray-50">
                                Petakan Akun
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                                Belum ada data header
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Drag and Drop Placeholder -->
        <div class="mt-8 border-2 border-dashed border-gray-300 rounded-[--radius-card] p-8 text-center">
            <i class='bx bx-move-horizontal text-4xl text-gray-300 mb-3'></i>
            <h3 class="text-lg font-medium text-gray-900">Seret & Jatuhkan Pemetaan</h3>
            <p class="text-gray-600 mt-2">Seret akun COA ke header untuk menugaskannya</p>
            <p class="text-sm text-gray-500 mt-2 italic">Segera Hadir</p>
        </div>
    </div>

    <!-- Import Tab -->
    <div x-show="activeTab === 'import'" class="space-y-6">
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-[--radius-button] text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-[--radius-button] text-sm">
                {!! session('error') !!}
            </div>
        @endif

        <!-- Upload Area -->
        <div class="bg-white rounded-[--radius-card] shadow-card p-8">
            <form method="POST" action="{{ route($routePrefix . '/master-data/coa-workspace.import') }}" enctype="multipart/form-data" id="import-form">
                @csrf

                <div class="border-2 border-dashed border-gray-300 rounded-[--radius-card] p-12 text-center hover:border-primary-400 transition-colors bg-gray-50" id="drop-zone">
                    <i class='bx bx-cloud-upload text-5xl text-gray-300 mb-4'></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Seret & Jatuhkan File Excel atau CSV</h3>
                    <p class="text-gray-600 mb-4">atau klik untuk menelusuri</p>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" class="hidden" id="import-file-input" required>
                    <button type="button" onclick="document.getElementById('import-file-input').click()" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-[--radius-button] hover:bg-primary-600 transition-colors text-sm font-medium">
                        <i class='bx bx-folder'></i>
                        <span>Jelajahi File</span>
                    </button>
                    <p class="text-sm text-gray-500 mt-3" id="file-name-display">Tidak ada file dipilih</p>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pratinjau File</label>
                    <div id="preview-container" class="hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-gray-600">
                                <thead class="bg-gray-50 text-gray-700 uppercase">
                                    <tr>
                                        <th class="px-4 py-3 font-semibold">Kode</th>
                                        <th class="px-4 py-3 font-semibold">Nama</th>
                                        <th class="px-4 py-3 font-semibold">Kategori</th>
                                        <th class="px-4 py-3 font-semibold">Header</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100" id="preview-rows">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-[--radius-button] hover:bg-primary-600 transition-colors text-sm font-medium">
                        <i class='bx bx-import'></i>
                        <span>Impor Data</span>
                    </button>
                    <a href="{{ route($routePrefix . '/master-data/coa-workspace.template') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-100 text-gray-700 rounded-[--radius-button] hover:bg-gray-200 transition-colors text-sm font-medium">
                        <i class='bx bx-download'></i>
                        <span>Unduh Template</span>
                    </a>
                </div>
            </form>
        </div>

        <!-- Import Guidelines -->
        <div class="bg-white rounded-[--radius-card] shadow-card p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Panduan Impor</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Kolom yang Diperlukan:</h4>
                    <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                        <li>Account Code (kode_akun)</li>
                        <li>Account Name (nama_akun)</li>
                        <li>Category (asset/liability/equity/revenue/expense)</li>
                        <li>Normal Balance (debit/credit)</li>
                        <li>Header Name or Code</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-2">Format yang Didukung:</h4>
                    <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                        <li>Excel (.xlsx)</li>
                        <li>CSV (UTF-8 encoded)</li>
                        <li>Max 1000 rows per import</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Tab -->
    <div x-show="activeTab === 'export'" class="bg-white rounded-[--radius-card] shadow-card p-8">
        <div class="max-w-2xl">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Ekspor Kode Akun</h2>

            <form method="POST" action="{{ route($routePrefix . '/master-data/coa-workspace.export') }}">
                @csrf

                <!-- Export Format -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Format Ekspor</label>
                    <div class="space-y-3">
                        <label class="flex items-center gap-3 p-3 border border-gray-300 rounded-[--radius-button] hover:border-primary transition-colors cursor-pointer">
                            <input type="radio" name="format" value="excel" checked class="w-4 h-4 text-primary">
                            <span>Excel (.xlsx)</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 border border-gray-300 rounded-[--radius-button] hover:border-primary transition-colors cursor-pointer">
                            <input type="radio" name="format" value="csv" class="w-4 h-4 text-primary">
                            <span>CSV (.csv)</span>
                        </label>
                    </div>
                </div>

                <!-- Include Options -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Sertakan</label>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="include_headers" value="1" checked class="w-4 h-4 text-primary rounded border-gray-300">
                            <span class="text-sm text-gray-700">Penugasan header</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="include_audit" value="1" class="w-4 h-4 text-primary rounded border-gray-300">
                            <span class="text-sm text-gray-700">Informasi audit</span>
                        </label>
                    </div>
                </div>

                <!-- Export Button -->
                <div>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-[--radius-button] hover:bg-primary-600 transition-colors text-sm font-medium">
                        <i class='bx bx-export'></i>
                        <span>Ekspor Data</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Audit History Tab -->
    <div x-show="activeTab === 'audit-history'" class="bg-white rounded-[--radius-card] shadow-card p-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Riwayat Audit</h2>
            <p class="text-sm text-gray-600">Lacak perubahan pada kode akun dan header</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase">
                    <tr>
                            <th class="px-4 py-3 font-semibold">Pengguna</th>
                            <th class="px-4 py-3 font-semibold">Aksi</th>
                            <th class="px-4 py-3 font-semibold">Model</th>
                            <th class="px-4 py-3 font-semibold">Perubahan</th>
                            <th class="px-4 py-3 font-semibold">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <!-- Placeholder - would use ActivityLog model if available -->
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                            Audit logging tidak tersedia. Catatan aktivitas akan muncul di sini setelah diaktifkan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-4 text-sm text-gray-600">
            <p class="mb-2">Menampilkan 50 catatan terakhir</p>
            <div class="flex gap-2">
                <button class="px-3 py-1 border border-gray-300 rounded-lg text-gray-400 cursor-not-allowed">Sebelumnya</button>
                <button class="px-3 py-1 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Selanjutnya</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function() {
        const fileInput = document.getElementById('import-file-input');
        const fileNameDisplay = document.getElementById('file-name-display');
        const dropZone = document.getElementById('drop-zone');

        if (!fileInput) return;

        // File selection via browse button
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                fileNameDisplay.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
            } else {
                fileNameDisplay.textContent = 'Tidak ada file dipilih';
            }
        });

        // Drag and drop
        if (dropZone) {
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropZone.classList.add('border-primary-400', 'bg-primary-50');
                });
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropZone.classList.remove('border-primary-400', 'bg-primary-50');
                });
            });

            dropZone.addEventListener('drop', function(e) {
                const file = e.dataTransfer.files[0];
                if (file && (file.name.endsWith('.xlsx') || file.name.endsWith('.xls') || file.name.endsWith('.csv'))) {
                    fileInput.files = e.dataTransfer.files;
                    fileNameDisplay.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                    const event = new Event('change', { bubbles: true });
                    fileInput.dispatchEvent(event);
                }
            });
        }
    })();
</script>
@endpush
