@props(['actions' => []])

<div class="card">
    <div class="card-header">
        <div class="flex items-center gap-2">
            <i data-lucide="zap" class="w-4 h-4 text-primary"></i>
            <h5 class="font-semibold text-sm">Aksi Cepat</h5>
        </div>
    </div>
    <div class="card-body">
        @if(count($actions) > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @foreach($actions as $action)
            <a href="{{ $action['route'] ?? '#' }}" class="btn-secondary text-xs gap-2 justify-center">
                <i data-lucide="{{ $action['icon'] ?? 'circle' }}" class="w-4 h-4"></i>
                {{ $action['label'] ?? '' }}
            </a>
            @endforeach
        </div>
        @else
        <p class="text-xs text-gray-400 text-center py-2">Tidak ada aksi cepat</p>
        @endif
    </div>
</div>
