@props(['roleLabel' => ''])

<div class="overflow-hidden rounded-card" style="background: linear-gradient(135deg, #1D4ED8 0%, #2563EB 100%);">
    <div class="px-6 py-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="badge bg-white/20 text-white border-0 text-[10px]">{{ $roleLabel }}</span>
            </div>
            <h2 class="text-white font-bold text-lg tracking-tight">Selamat Datang, {{ Auth::user()->name }}</h2>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-white/50 text-xs">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
        </div>
    </div>
</div>
