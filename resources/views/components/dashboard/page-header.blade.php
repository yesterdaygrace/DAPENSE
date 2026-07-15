@props(['title' => '', 'description' => '', 'actions' => ''])

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900 tracking-tight">{{ $title ?: $__env->yieldContent('title') }}</h1>
        @if($description)
        <p class="text-sm text-gray-500 mt-0.5">{{ $description }}</p>
        @endif
    </div>
    @if($actions)
    <div class="flex items-center gap-2 flex-shrink-0">{!! $actions !!}</div>
    @endif
</div>
