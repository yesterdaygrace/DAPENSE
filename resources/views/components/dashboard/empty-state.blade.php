@props(['icon' => 'inbox', 'title' => 'Tidak ada data', 'description' => '', 'action' => null])

<div class="flex flex-col items-center justify-center py-16 text-center">
    <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mb-5">
        <i data-lucide="{{ $icon }}" class="w-7 h-7 text-gray-400"></i>
    </div>
    <h3 class="text-base font-semibold text-gray-900 mb-1">{{ $title }}</h3>
    @if($description)
    <p class="text-sm text-gray-500 max-w-sm">{{ $description }}</p>
    @endif
    @if($action)
    <div class="mt-4">{{ $action }}</div>
    @endif
</div>
