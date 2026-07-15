@props(['icon', 'title', 'value', 'trend' => null, 'trendLabel' => '', 'color' => '#1D4ED8', 'bg' => 'rgba(29,78,216,0.08)'])

<div class="stat-card">
    <div class="flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background: {{ $bg }};">
            <i data-lucide="{{ $icon }}" style="width: 20px; height: 20px; color: {{ $color }};"></i>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-xs text-gray-500 mb-0.5">{{ $title }}</p>
            <h4 class="font-bold text-lg text-gray-900 tracking-tight mb-0">{{ $value ?: '—' }}</h4>
            @if($trend !== null)
            <span class="inline-flex items-center gap-1 text-xs {{ $trend >= 0 ? 'text-green-600' : 'text-red-600' }}">
                <i data-lucide="{{ $trend >= 0 ? 'trending-up' : 'trending-down' }}" class="w-3 h-3"></i>
                {{ abs($trend) }}% {{ $trendLabel ?: 'vs bulan lalu' }}
            </span>
            @endif
        </div>
    </div>
</div>
