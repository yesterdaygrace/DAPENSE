@props(['name', 'route', 'icon', 'desc' => '', 'badge' => null, 'metadata' => null])

<a href="{{ route($route) }}" class="block no-underline group">
    <div class="card text-center h-full transition-all duration-300 group-hover:-translate-y-1 group-hover:shadow-card-hover relative">
        @if($badge)
        <span class="badge badge-primary absolute top-3 right-3">{{ $badge }}</span>
        @endif
        <div class="px-4 py-6">
            <div class="w-12 h-12 rounded-xl bg-primary-50 flex items-center justify-center mx-auto mb-3">
                <i class="{{ $icon }}" style="font-size: 1.4rem; color: #1D4ED8;"></i>
            </div>
            <h6 class="font-semibold text-sm text-gray-900 mb-1">{{ $name }}</h6>
            @if($desc)
            <p class="text-xs text-gray-500 mb-0">{{ $desc }}</p>
            @endif
            @if($metadata)
            <p class="text-[10px] text-gray-400 mt-1 mb-0">{{ $metadata }}</p>
            @endif
        </div>
    </div>
</a>
