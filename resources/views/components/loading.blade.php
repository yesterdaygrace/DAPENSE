<div
    x-data="{ loading: false }"
    @loading-start.window="loading = true"
    @loading-stop.window="loading = false"
    x-show="loading"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[1070] flex items-center justify-center bg-white/60 backdrop-blur-[1px]"
    style="display: none;"
    role="status"
    aria-label="Loading"
>
    <div class="flex flex-col items-center gap-3">
        <div class="w-10 h-10 border-[3px] border-gray-200 border-t-primary rounded-full animate-spin"></div>
        <p class="text-sm font-medium text-gray-500">Memproses...</p>
    </div>
</div>
