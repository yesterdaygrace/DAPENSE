<div class="relative" x-data="{ open: false, notifications: [
    { id: 1, icon: 'file-text', iconColor: 'text-primary', title: 'Periode baru telah ditambahkan', time: '2 jam lalu', read: false },
    { id: 2, icon: 'check-circle', iconColor: 'text-success', title: 'Jurnal berhasil diposting', time: '5 jam lalu', read: false },
], get unreadCount() { return this.notifications.filter(n => !n.read).length; }, markAllRead() { this.notifications.forEach(n => n.read = true); } }">
    <button @click="open = !open" class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors" aria-label="Notifications">
        <i data-lucide="bell" class="w-5 h-5"></i>
        <template x-if="unreadCount > 0">
            <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center" x-text="unreadCount"></span>
        </template>
    </button>

    <div x-show="open" @click.outside="open = false" class="dropdown-menu origin-top-right w-[320px]" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
        <div class="px-3 py-2.5 border-b border-gray-100 flex items-center justify-between">
            <h6 class="text-xs font-semibold text-gray-900">Notifikasi</h6>
            <button @click="markAllRead()" x-show="unreadCount > 0" class="text-[10px] text-primary hover:text-primary-700 font-medium cursor-pointer">Tandai semua dibaca</button>
        </div>
        <div class="py-1 max-h-64 overflow-y-auto">
            <template x-for="n in notifications" :key="n.id">
                <button @click="n.read = true" class="dropdown-item gap-2.5 w-full text-left" :class="{ 'bg-primary-50/40': !n.read }">
                    <i :data-lucide="n.icon" :class="[n.iconColor, 'w-4 h-4 mt-0.5 flex-shrink-0']"></i>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-700" x-text="n.title"></p>
                        <p class="text-[10px] text-gray-400" x-text="n.time"></p>
                    </div>
                    <template x-if="!n.read">
                        <span class="w-2 h-2 rounded-full bg-primary flex-shrink-0"></span>
                    </template>
                </button>
            </template>
            <template x-if="notifications.length === 0">
                <p class="text-xs text-gray-400 text-center py-6">Tidak ada notifikasi</p>
            </template>
        </div>
    </div>
</div>
