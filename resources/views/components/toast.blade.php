<div
    x-data="{
        toasts: [],
        add(type, message) {
            const id = Date.now();
            this.toasts.push({ id, type, message });
            setTimeout(() => this.remove(id), 3000);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }"
    @toast.window="add($event.detail.type, $event.detail.message)"
    x-init="
        @if(session('success'))
            add('success', '{{ session('success') }}');
        @endif
        @if(session('error'))
            add('error', '{{ session('error') }}');
        @endif
    "
    aria-live="polite"
    aria-atomic="true"
    class="fixed top-4 right-4 z-[1080] flex flex-col gap-2 pointer-events-none"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.id"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-4"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-4"
            :class="{
                'toast-success': toast.type === 'success',
                'toast-danger': toast.type === 'error',
                'toast-warning': toast.type === 'warning',
                'toast-info': toast.type === 'info',
            }"
            class="pointer-events-auto flex items-center gap-3 bg-white rounded-card shadow-elevated border border-gray-100 px-4 py-3 min-w-[300px] max-w-md"
            role="alert"
        >
            <template x-if="toast.type === 'success'">
                <i data-lucide="check-circle" class="w-5 h-5 text-success flex-shrink-0"></i>
            </template>
            <template x-if="toast.type === 'error'">
                <i data-lucide="alert-circle" class="w-5 h-5 text-danger flex-shrink-0"></i>
            </template>
            <template x-if="toast.type === 'warning'">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-warning flex-shrink-0"></i>
            </template>
            <template x-if="toast.type === 'info'">
                <i data-lucide="info" class="w-5 h-5 text-primary flex-shrink-0"></i>
            </template>
            <p class="text-sm text-gray-800 flex-1 mb-0" x-text="toast.message"></p>
            <button @click="remove(toast.id)" class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    </template>
</div>
