<div class="space-y-6">
    <x-toast />

    <div class="page-header">
        <div>
            <h1 class="page-title">Jurnaling</h1>
            <p class="page-subtitle">Kelola jurnal transaksi per jenis</p>
        </div>
        <button wire:click="create()" class="btn btn-primary">
            <i data-lucide="plus" class="w-4 h-4"></i> Entri Baru
        </button>
    </div>

    {{-- Type Tabs --}}
    <div class="flex border-b border-gray-200 gap-1 overflow-x-auto">
        @foreach(['km' => 'Kas Masuk', 'kk' => 'Kas Keluar', 'bm' => 'Bank Masuk', 'bk' => 'Bank Keluar', 'mem' => 'Memorial', 'mempenutup' => 'Memorial Penutup'] as $key => $label)
        <button
            wire:click="$set('typeFilter', '{{ $key }}')"
            class="px-4 py-2 text-sm font-medium border-b-2 transition-colors whitespace-nowrap
                {{ $typeFilter === $key ? 'text-primary border-primary bg-primary-50/50' : 'text-gray-500 hover:text-gray-700 border-transparent' }}"
        >
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="filter-card p-4">
        <div class="filter-row">
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
                <label class="label">Cari Nomor Bukti</label>
                <input type="text" wire:model.live.debounce.300ms="search" class="input-field" placeholder="KM-0001...">
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
                    <th>Akun</th>
                    <th>Debit</th>
                    <th>Kredit</th>
                    <th>Keterangan</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entries as $entry)
                <tr>
                    <td class="font-medium">{{ $entry->nomor_bukti }}</td>
                    <td>{{ \Carbon\Carbon::parse($entry->tanggal_jurnal)->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge badge-neutral">{{ $entry->coa->kode_akun ?? '—' }}</span>
                        {{ Str::limit($entry->coa->nama_akun ?? '—', 30) }}
                    </td>
                    <td class="tabular-nums">Rp {{ number_format($entry->debit, 0, ',', '.') }}</td>
                    <td class="tabular-nums">Rp {{ number_format($entry->kredit, 0, ',', '.') }}</td>
                    <td>{{ Str::limit($entry->keterangan, 30) }}</td>
                    <td class="text-right space-x-1">
                        <button wire:click="edit({{ $entry->id }})" class="btn btn-ghost btn-sm"><i data-lucide="pencil" class="w-4 h-4"></i></button>
                        <button wire:click="confirmDelete({{ $entry->id }})" class="btn btn-danger btn-sm"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-8 text-gray-500">Belum ada jurnal untuk kategori ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $entries->links() }}</div>

    {{-- Create/Edit Modal --}}
    @if($showModal)
    <div class="modal-backdrop" wire:click.self="$set('showModal', false)">
        <div class="modal-content">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="text-lg font-semibold">{{ $editing ? 'Edit' : 'Entri Baru' }} Jurnal ({{ $typeFilter }})</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form wire:submit="save()" class="p-5 space-y-4">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="label">Tanggal</label>
                        <input type="date" wire:model="formData.tanggal_jurnal" class="input-field" required>
                    </div>
                    <div>
                        <label class="label">Nomor Bukti</label>
                        <input type="text" wire:model="formData.nomor_bukti" class="input-field" readonly>
                    </div>
                    <div>
                        <label class="label">Periode</label>
                        <select wire:model="formData.periode_id" class="select-field" required>
                            <option value="">Pilih</option>
                            @foreach($periodes as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_periode }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="label">Akun</label>
                    <select wire:model="formData.coa_id" class="select-field" required>
                        <option value="">Pilih Akun</option>
                        @foreach($coas as $coa)
                            <option value="{{ $coa->id }}">{{ $coa->kode_akun }} — {{ $coa->nama_akun }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Keterangan</label>
                    <textarea wire:model="formData.keterangan" class="input-field" rows="2"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Debit</label>
                        <input type="number" wire:model="formData.debit" step="0.01" min="0" class="input-field" required>
                    </div>
                    <div>
                        <label class="label">Kredit</label>
                        <input type="number" wire:model="formData.kredit" step="0.01" min="0" class="input-field" required>
                    </div>
                </div>
                <div class="flex gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" class="btn btn-primary"><i data-lucide="check" class="w-4 h-4"></i> Simpan</button>
                    <button type="button" wire:click="$set('showModal', false)" class="btn btn-ghost">Batal</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Delete Confirmation --}}
    @if($showDeleteModal)
    <div class="modal-backdrop" wire:click.self="$set('showDeleteModal', false)">
        <div class="modal-content">
            <div class="p-5 text-center">
                <i data-lucide="alert-triangle" class="w-12 h-12 text-danger mx-auto mb-3"></i>
                <h3 class="text-lg font-semibold mb-2">Hapus Jurnal?</h3>
                <p class="text-gray-600 text-sm">Data akan dihapus permanen.</p>
                <div class="flex gap-3 justify-center mt-6">
                    <button wire:click="deleteEntry()" class="btn btn-danger"><i data-lucide="trash-2" class="w-4 h-4"></i> Hapus</button>
                    <button wire:click="$set('showDeleteModal', false)" class="btn btn-ghost">Batal</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
