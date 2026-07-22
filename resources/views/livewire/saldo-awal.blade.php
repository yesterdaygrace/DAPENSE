<div class="space-y-6">
    <x-toast />

    <div class="page-header">
        <div>
            <h1 class="page-title">Saldo Awal</h1>
            <p class="page-subtitle">Atur saldo awal akun per periode</p>
        </div>
        <button wire:click="create()" class="btn btn-primary">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Saldo Awal
        </button>
    </div>

    {{-- Filter --}}
    <div class="filter-card p-4">
        <div class="filter-row">
            <div class="filter-group max-w-sm">
                <label class="label">Filter Periode</label>
                <select wire:model="periodeFilter" class="select-field">
                    <option value="">Semua Periode</option>
                    @foreach($periodes as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_periode }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kode Akun</th>
                    <th>Nama Akun</th>
                    <th>Periode</th>
                    <th>Tanggal</th>
                    <th class="text-right">Debit</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($saldoAwals as $sa)
                <tr>
                    <td class="font-medium">{{ $sa->coa->kode_akun ?? '—' }}</td>
                    <td>{{ $sa->coa->nama_akun ?? '—' }}</td>
                    <td><span class="badge badge-neutral">{{ $sa->periode->nama_periode ?? '—' }}</span></td>
                    <td>{{ $sa->tanggal_saldo ? \Carbon\Carbon::parse($sa->tanggal_saldo)->format('d/m/Y') : '—' }}</td>
                    <td class="text-right tabular-nums">Rp {{ number_format($sa->debit, 0, ',', '.') }}</td>
                    <td class="text-right space-x-1">
                        @if($this->canAccess('master-data'))
                        <button wire:click="edit({{ $sa->id }})" class="btn btn-ghost btn-sm"><i data-lucide="pencil" class="w-4 h-4"></i></button>
                        <button wire:click="confirmDelete({{ $sa->id }})" class="btn btn-danger btn-sm"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-gray-500">Belum ada saldo awal</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal --}}
    @if($showModal)
    <div class="modal-backdrop" wire:click.self="$set('showModal', false)">
        <div class="modal-content">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="text-lg font-semibold">{{ $editing ? 'Edit' : 'Tambah' }} Saldo Awal</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form wire:submit="save()" class="p-5 space-y-4">
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
                    <label class="label">Periode</label>
                    <select wire:model="formData.periode_id" class="select-field" required>
                        <option value="">Pilih Periode</option>
                        @foreach($periodes as $p)
                            <option value="{{ $p->id }}">{{ $p->nama_periode }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Tanggal Saldo</label>
                    <input type="date" wire:model="formData.tanggal_saldo" class="input-field" required>
                </div>
                <div>
                    <label class="label">Debit</label>
                    <input type="number" wire:model="formData.debit" step="0.01" min="0" class="input-field" required>
                </div>
                <div class="flex gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" class="btn btn-primary"><i data-lucide="check" class="w-4 h-4"></i> Simpan</button>
                    <button type="button" wire:click="$set('showModal', false)" class="btn btn-ghost">Batal</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Delete --}}
    @if($showDeleteModal)
    <div class="modal-backdrop" wire:click.self="$set('showDeleteModal', false)">
        <div class="modal-content p-5 text-center">
            <i data-lucide="alert-triangle" class="w-12 h-12 text-danger mx-auto mb-3"></i>
            <h3 class="text-lg font-semibold mb-2">Hapus Saldo Awal?</h3>
            <p class="text-gray-600 text-sm">Data akan dihapus permanen.</p>
            <div class="flex gap-3 justify-center mt-6">
                <button wire:click="deleteEntry()" class="btn btn-danger">Hapus</button>
                <button wire:click="$set('showDeleteModal', false)" class="btn btn-ghost">Batal</button>
            </div>
        </div>
    </div>
    @endif
</div>
