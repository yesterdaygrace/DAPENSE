<div class="space-y-6">
    <x-toast />

    <div class="page-header">
        <div>
            <h1 class="page-title">Buku Besar</h1>
            <p class="page-subtitle">Ringkasan akun per buku besar</p>
        </div>
        <a href="{{ route($this->routePrefix() . '.bukubesar.export') }}" class="btn btn-primary">
            <i data-lucide="download" class="w-4 h-4"></i> Ekspor
        </a>
    </div>

    {{-- Filters --}}
    <div class="filter-card p-4">
        <div class="filter-row">
            <div class="filter-group">
                <label class="label">Periode</label>
                <select wire:model="periodeId" class="select-field">
                    <option value="">Semua Periode</option>
                    @foreach($periodes as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_periode }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label class="label">Akun</label>
                <select wire:model="coaId" class="select-field">
                    <option value="">Semua Akun</option>
                    @foreach($coas as $coa)
                        <option value="{{ $coa->id }}">{{ $coa->kode_akun }} — {{ $coa->nama_akun }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label class="label">Dari Tanggal</label>
                <input type="date" wire:model="startDate" class="input-field">
            </div>
            <div class="filter-group">
                <label class="label">Sampai Tanggal</label>
                <input type="date" wire:model="endDate" class="input-field">
            </div>
            <div class="flex items-end">
                <button wire:click="loadEntries()" class="btn btn-primary">
                    <i data-lucide="search" class="w-4 h-4"></i> Cari
                </button>
            </div>
        </div>
    </div>

    {{-- Results --}}
    @if(count($entries) > 0)
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nomor Bukti</th>
                    <th>Akun</th>
                    <th>Keterangan</th>
                    <th class="text-right">Debit</th>
                    <th class="text-right">Kredit</th>
                    <th class="text-right">Saldo</th>
                </tr>
            </thead>
            <tbody>
                @php $balance = 0; @endphp
                @foreach($entries as $entry)
                @php $balance += $entry->debit - $entry->kredit; @endphp
                <tr>
                    <td>{{ \Carbon\Carbon::parse($entry->tanggal_jurnal)->format('d/m/Y') }}</td>
                    <td class="font-medium">{{ $entry->nomor_bukti }}</td>
                    <td><span class="badge badge-neutral">{{ $entry->coa->kode_akun ?? '—' }}</span></td>
                    <td>{{ Str::limit($entry->keterangan, 35) }}</td>
                    <td class="text-right tabular-nums">{{ $entry->debit > 0 ? 'Rp ' . number_format($entry->debit, 0, ',', '.') : '—' }}</td>
                    <td class="text-right tabular-nums">{{ $entry->kredit > 0 ? 'Rp ' . number_format($entry->kredit, 0, ',', '.') : '—' }}</td>
                    <td class="text-right tabular-nums font-medium {{ $balance >= 0 ? 'text-success' : 'text-danger' }}">
                        Rp {{ number_format(abs($balance), 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-gray-300 font-semibold">
                    <td colspan="4" class="text-right">Total</td>
                    <td class="text-right tabular-nums">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                    <td class="text-right tabular-nums">Rp {{ number_format($totalKredit, 0, ',', '.') }}</td>
                    <td class="text-right tabular-nums">Rp {{ number_format(abs($totalDebit - $totalKredit), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @elseif($periodeId || $coaId || $startDate)
    <div class="card">
        <div class="card-body text-center py-12 text-gray-500">
            <i data-lucide="search-x" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
            <p class="font-medium">Tidak ada data untuk filter ini</p>
            <p class="text-sm mt-1">Coba ubah periode atau akun yang dipilih.</p>
        </div>
    </div>
    @endif
</div>
