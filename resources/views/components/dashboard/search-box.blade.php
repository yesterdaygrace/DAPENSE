<div class="relative w-60">
    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
    <input type="text" class="input-field !pl-9 !pr-12 !py-2 !text-sm !bg-gray-50 !border-transparent" placeholder="Cari..." x-data x-init="
        $el.addEventListener('keydown', e => { if ((e.ctrlKey || e.metaKey) && e.key === 'k') { e.preventDefault(); $el.focus(); } });
    ">
    <kbd class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] bg-gray-200 px-1.5 py-0.5 rounded text-gray-500 font-mono">Ctrl+K</kbd>
</div>
