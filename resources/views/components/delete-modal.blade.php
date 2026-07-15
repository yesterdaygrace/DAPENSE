@props(['title' => 'Konfirmasi Hapus', 'message' => 'Apakah Anda yakin ingin menghapus item ini?'])

<div x-data="{ show: false, url: '' }"
     x-on:delete-modal-open.window="show = true; url = $event.detail"
     x-on:delete-modal-close.window="show = false"
     x-show="show"
     x-cloak
     class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click.self="show = false">
    <div class="bg-white rounded-modal shadow-elevated-lg w-full max-w-sm p-6"
         x-show="show"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-danger-50 flex items-center justify-center flex-shrink-0">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-danger"></i>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900">{{ $title }}</h3>
                <p class="text-sm text-gray-500 mt-0.5">{{ $message }}</p>
            </div>
        </div>
        <div class="flex justify-end gap-2">
            <button type="button" class="btn-secondary text-sm" @click="show = false">Batal</button>
            <a :href="url" class="btn-danger text-sm">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
                Hapus
            </a>
        </div>
    </div>
</div>
