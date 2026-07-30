@extends('layouts.applayout')

@section('title', 'Aktivitas Terbaru')

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
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-600">
        <a href="{{ $prefix }}/dashboard" class="hover:text-primary transition-colors">Dasbor</a>
        <i data-lucide='chevron-right' class='text-gray-400' aria-hidden="true"></i>
        <span class="text-gray-900 font-medium">Aktivitas Terbaru</span>
    </nav>

    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Aktivitas Terbaru</h1>
            <p class="mt-2 text-gray-600">Log aktivitas pengguna dan sistem secara real-time</p>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Aktivitas</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total'], 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-blue-50 flex items-center justify-center">
                    <i data-lucide='history' class='text-2xl text-blue-600' aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['today'], 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-green-50 flex items-center justify-center">
                    <i data-lucide='calendar-check' class='text-2xl text-green-600' aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Login</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['logins'], 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-purple-50 flex items-center justify-center">
                    <i data-lucide='log-in-circle' class='text-2xl text-purple-600' aria-hidden="true"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[--radius-card] p-6 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Data Dibuat</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['creates'], 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 rounded-[--radius-button] bg-amber-50 flex items-center justify-center">
                    <i data-lucide='plus-circle' class='text-2xl text-amber-600' aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Search --}}
    <div class="bg-white rounded-[--radius-card] p-4 shadow-card">
        <form method="GET" action="{{ route('activity') }}" class="flex items-center gap-3">
            <div class="relative flex-1">
                <i data-lucide='search' class='absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg' aria-hidden="true"></i>
                <input type="text" name="search" value="{{ $search ?? '' }}"
                       placeholder="Cari aktivitas berdasarkan deskripsi atau pengguna..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-[--radius-button] border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
            </div>
            <button type="submit" class="btn-primary px-5 py-2.5 text-sm font-medium">Cari</button>
            @if($search)
            <a href="{{ route('activity') }}" class="btn-ghost px-4 py-2.5 text-sm font-medium">Reset</a>
            @endif
        </form>
    </div>

    {{-- Activity List --}}
    <div class="bg-white rounded-[--radius-card] shadow-card">
        @if($activities->count() > 0)
        <div class="divide-y divide-gray-100">
            @foreach($activities as $activity)
            @php
                $icon = match(true) {
                    str_contains($activity->description, 'login') || str_contains($activity->description, 'logout') => 'log-in',
                    $activity->event === 'created' => 'plus-circle',
                    $activity->event === 'updated' || $activity->event === 'deleted' => 'edit',
                    str_contains($activity->description, 'Jurnal') || str_contains($activity->description, 'jurnal') => 'file-text',
                    str_contains($activity->description, 'Saldo') || str_contains($activity->description, 'saldo') => 'wallet',
                    default => 'check-circle',
                };
                $iconBg = match(true) {
                    str_contains($activity->description, 'login') => 'rgba(139,92,246,0.1)',
                    str_contains($activity->description, 'logout') => 'rgba(239,68,68,0.1)',
                    $activity->event === 'created' => 'rgba(16,185,129,0.1)',
                    $activity->event === 'deleted' => 'rgba(239,68,68,0.1)',
                    $activity->event === 'updated' => 'rgba(245,158,11,0.1)',
                    str_contains($activity->description, 'Jurnal') || str_contains($activity->description, 'jurnal') => 'rgba(59,130,246,0.1)',
                    default => 'rgba(107,114,128,0.1)',
                };
                $iconColor = match(true) {
                    str_contains($activity->description, 'login') => '#8B5CF6',
                    str_contains($activity->description, 'logout') => '#EF4444',
                    $activity->event === 'created' => '#10B981',
                    $activity->event === 'deleted' => '#EF4444',
                    $activity->event === 'updated' => '#F59E0B',
                    str_contains($activity->description, 'Jurnal') || str_contains($activity->description, 'jurnal') => '#3B82F6',
                    default => '#6B7280',
                };
            @endphp
            <div class="flex items-start gap-4 px-6 py-4 hover:bg-gray-50 transition-colors">
                <div class="w-10 h-10 rounded-[--radius-button] flex items-center justify-center flex-shrink-0" style="background: {{ $iconBg }};">
                    <i class="{{ $icon }}" style="color: {{ $iconColor }}; font-size: 1.2rem;"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start gap-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $activity->description }}</p>
                            @if($activity->causer)
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $activity->causer->name }}
                                @if($activity->causer->email)
                                · {{ $activity->causer->email }}
                                @endif
                            </p>
                            @endif
                            @if($activity->properties && $activity->properties->count() > 0)
                            <div class="mt-1.5 flex flex-wrap gap-1.5">
                                @foreach($activity->properties as $key => $value)
                                @if(is_string($value) || is_numeric($value))
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600">
                                    {{ $key }}: {{ $value }}
                                </span>
                                @endif
                                @endforeach
                            </div>
                            @endif
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs text-gray-400 whitespace-nowrap">{{ $activity->created_at->diffForHumans() }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">{{ $activity->created_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $activities->links() }}
        </div>
        @else
        <div class="py-16 text-center">
            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <i data-lucide='history' class='text-3xl text-gray-400' aria-hidden="true"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Belum Ada Aktivitas</h3>
            <p class="text-sm text-gray-500">Log aktivitas akan muncul setelah ada aktivitas pengguna atau sistem</p>
        </div>
        @endif
    </div>
</div>
@endsection
