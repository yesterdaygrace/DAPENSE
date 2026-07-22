<div class="space-y-6">
    <x-toast />

    <div class="page-header">
        <div>
            <h1 class="page-title">Daftar Jurnal</h1>
            <p class="page-subtitle">Lihat dan cari semua entri jurnal</p>
        </div>
        <a href="{{ route($this->routePrefix() . '.jurnal-entry') }}" class="btn btn-primary">
            <i data-lucide="plus" class="w-4 h-4"></i> Entri Baru
        </a>
    </div>

    {{-- Filters --}}
    <div class="filter-card p-4">
        <div class="filter-row">
            <div class="filter-group">
                <label class="label">Tipe Jurnal</label>
                <select wire:model="typeFilter" class="select-field">
                    <option value="">Semua Tipe</option>
                    <option value="km">Kas Masuk</option>
                    <option value="kk">Kas Keluar</option>
                    <option value="bm">Bank Masuk</option>
                    <option value="bk">Bank Keluar</option>
                    <option value="mem">Memorial</option>
                    <option value="mempenutup">Memorial Penutup</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="label">Periode</label>
                <select wire:model="periodeFilter" class="select-field">
                    <option value="">Semua Periode</option>
                    @foreach($periodes as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_periode }}</option>
                    @endforeach
                </select>
            </div>
            <div class="filter-group">
                <label class="label">Cari</label>
                <input type="text" wire:model.live.debounce.300ms="search" class="input-field" placeholder="Nomor bukti atau keterangan...">
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nomor Bukti</th>
                    <th>Tanggal</th>
                    <th>Tipe</th>
                    <th>Akun</th>
                    <th class="text-right">Debit</th>
                    <th class="text-right">Kredit</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $entry)
                <tr>
                    <td class="font-medium">{{ $entry->nomor_bukti }}</td>
                    <td>{{ \Carbon\Carbon::parse($entry->tanggal_jurnal)->format('d/m/Y') }}</td>
                    <td>
                        @php
                            $typeColors = [
                                'km' => 'badge-success',
                                'kk' => 'badge-danger',
                                'bm' => 'badge-primary',
                                'bk' => 'badge-warning',
                                'mem' => 'badge-neutral',
                                'mempenutup' => 'badge-neutral',
                            ];
                        @endphp
                        <span class="badge {{ $typeColors[$entry->kategori_jurnal] ?? 'badge-neutral' }}">
                            {{ strtoupper($entry->kategori_jurnal) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-neutral">{{ $entry->coa->kode_akun ?? '—' }}</span>
                        {{ Str::limit($entry->coa->nama_akun ?? '—', 25) }}
                    </td>
                    <td class="text-right tabular-nums">Rp {{ number_format($entry->debit, 0, ',', '.') }}</td>
                    <td class="text-right tabular-nums">Rp {{ number_format($entry->kredit, 0, ',', '.') }}</td>
                    <td>{{ Str::limit($entry->keterangan, 35) }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-8 text-gray-500">Tidak ada data jurnal</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $entries->links() }}</div>
</div>
