@props(['activities'])

<div class="card h-full">
    <div class="card-header">
        <div class="flex items-center gap-2">
            <i data-lucide="activity" class="w-4 h-4 text-primary"></i>
            <h5 class="font-semibold text-sm">Aktivitas Terbaru</h5>
        </div>
        <a href="{{ route('jurnaling') }}" class="btn-ghost text-xs px-2.5 py-1">Lihat Semua</a>
    </div>
    <div class="card-body !p-0">
        @if($activities->count() > 0)
        <div class="divide-y divide-gray-50">
            @foreach($activities as $entry)
            @php
            $isDebit = $entry->debit > 0;
            $amount = $isDebit ? $entry->debit : $entry->kredit;
            $amountColor = $isDebit ? '#16A34A' : '#DC2626';
            $icon = $isDebit ? 'arrow-down-left' : 'arrow-up-right';
            $iconBg = $isDebit ? 'rgba(22,163,74,0.1)' : 'rgba(220,38,38,0.1)';
            @endphp
            <div class="flex items-start gap-3 px-5 py-3.5">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background: {{ $iconBg }};">
                    <i data-lucide="{{ $icon }}" class="w-4 h-4" style="color: {{ $amountColor }};"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start gap-2">
                        <div>
                            <p class="text-sm font-medium text-gray-900 mb-0 leading-tight">{{ $entry->keterangan }}</p>
                            <p class="text-xs text-gray-400 mb-0">{{ $entry->nomor_bukti }} · {{ $entry->kategori_jurnal }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="text-sm font-semibold" style="color: {{ $amountColor }};">Rp {{ number_format($amount, 0, ',', '.') }}</span>
                            <div class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($entry->tanggal_jurnal)->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <x-dashboard.empty-state icon="inbox" message="Belum ada aktivitas jurnal" />
        @endif
    </div>
</div>
