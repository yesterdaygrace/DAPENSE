<div class="space-y-6">
    <x-toast />

    <div class="page-header">
        <div>
            <h1 class="page-title">Neraca Saldo</h1>
            <p class="page-subtitle">Verifikasi saldo debit dan kredit per periode</p>
        </div>
        @if($periodeId && count($saldoEntries) > 0)
        <div class="flex gap-2">
            <a href="{{ route('neraca-saldo.exportexcel', $periodeId) }}" class="btn btn-ghost">
                <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> Excel
            </a>
            <a href="{{ route('neraca-saldo.exportpdf', $periodeId) }}" class="btn btn-ghost">
                <i data-lucide="file-text" class="w-4 h-4"></i> PDF
            </a>
        </div>
        @endif
    </div>

    {{-- Periode Selector --}}
    <div class="filter-card p-4">
        <div class="filter-row">
            <div class="filter-group max-w-md">
                <label class="label">Pilih Periode</label>
                <select wire:model="periodeId" class="select-field">
                    <option value="">— Pilih Periode —</option>
                    @foreach($periodes as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_periode }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @if(count($saldoEntries) > 0)
    {{-- Balance Indicator --}}
    <div class="card p-4">
        <div class="flex items-center gap-3">
            @if($isBalanced)
                <div class="w-10 h-10 rounded-lg bg-success-50 flex items-center justify-center">
                    <i data-lucide="check-circle-2" class="w-6 h-6 text-success"></i>
                </div>
                <div>
                    <p class="font-semibold text-success-700">Seimbang</p>
                    <p class="text-sm text-gray-500">Total debit = total kredit</p>
                </div>
            @else
                <div class="w-10 h-10 rounded-lg bg-danger-50 flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-danger"></i>
                </div>
                <div>
                    <p class="font-semibold text-danger-700">Tidak Seimbang</p>
                    <p class="text-sm text-gray-500">Selisih: Rp {{ number_format(abs($totalDebit - $totalKredit), 0, ',', '.') }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Table --}}
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kode Akun</th>
                    <th>Nama Akun</th>
                    <th class="text-right">Debit</th>
                    <th class="text-right">Kredit</th>
                </tr>
            </thead>
            <tbody>
                @forelse($saldoEntries as $entry)
                <tr>
                    <td class="font-medium">{{ $entry->kode }}</td>
                    <td>{{ $entry->nama }}</td>
                    <td class="text-right tabular-nums {{ $entry->debit > 0 ? 'text-gray-900' : 'text-gray-400' }}">
                        {{ $entry->debit > 0 ? 'Rp ' . number_format($entry->debit, 0, ',', '.') : '—' }}
                    </td>
                    <td class="text-right tabular-nums {{ $entry->kredit > 0 ? 'text-gray-900' : 'text-gray-400' }}">
                        {{ $entry->kredit > 0 ? 'Rp ' . number_format($entry->kredit, 0, ',', '.') : '—' }}
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center py-8 text-gray-500">Tidak ada data neraca saldo</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-gray-300 font-semibold text-base">
                    <td colspan="2" class="text-right">Total</td>
                    <td class="text-right tabular-nums text-success">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                    <td class="text-right tabular-nums text-danger">Rp {{ number_format($totalKredit, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @elseif($periodeId)
    <div class="card">
        <div class="card-body text-center py-12 text-gray-500">
            <i data-lucide="calculator" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
            <p class="font-medium">Tidak ada data untuk periode ini</p>
            <p class="text-sm mt-1">Pastikan ada jurnal yang sudah diposting untuk periode ini.</p>
        </div>
    </div>
    @endif
</div>
