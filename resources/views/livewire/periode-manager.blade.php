<div class="space-y-6">
    <x-toast />

    <div class="page-header">
        <div>
            <h1 class="page-title">Periode Akuntansi</h1>
            <p class="page-subtitle">Atur tahun fiskal dan periode akuntansi</p>
        </div>
        <button wire:click="create()" class="btn btn-primary">
            <i data-lucide="plus" class="w-4 h-4"></i> Periode Baru
        </button>
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama Periode</th>
                    <th>Tanggal Awal</th>
                    <th>Tanggal Akhir</th>
                    <th>Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($periodes as $p)
                <tr>
                    <td class="font-medium">{{ $p->nama_periode }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->tanggal_awal)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->tanggal_akhir)->format('d/m/Y') }}</td>
                    <td>
                        @if($p->is_rekap)
                            <span class="badge badge-success">Rekap</span>
                        @else
                            <span class="badge badge-warning">Aktif</span>
                        @endif
                    </td>
                    <td class="text-right space-x-1">
                        @if($this->canAccess('master-data'))
                        <button wire:click="edit({{ $p->id }})" class="btn btn-ghost btn-sm"><i data-lucide="pencil" class="w-4 h-4"></i></button>
                        <button wire:click="confirmDelete({{ $p->id }})" class="btn btn-danger btn-sm"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-8 text-gray-500">Belum ada periode</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($showModal)
    <div class="modal-backdrop" wire:click.self="$set('showModal', false)">
        <div class="modal-content">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="text-lg font-semibold">{{ $editing ? 'Edit' : 'Tambah' }} Periode</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form wire:submit="save()" class="p-5 space-y-4">
                <div><label class="label">Nama Periode</label><input type="text" wire:model="formData.nama_periode" class="input-field" placeholder="2026 - Periode 01"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="label">Tanggal Awal</label><input type="date" wire:model="formData.tanggal_awal" class="input-field" required></div>
                    <div><label class="label">Tanggal Akhir</label><input type="date" wire:model="formData.tanggal_akhir" class="input-field" required></div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" wire:model="formData.is_rekap" class="text-primary rounded">
                    <label class="text-sm">Sudah Direkap</label>
                </div>
                <div class="flex gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" class="btn btn-primary"><i data-lucide="check" class="w-4 h-4"></i> Simpan</button>
                    <button type="button" wire:click="$set('showModal', false)" class="btn btn-ghost">Batal</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if($showDeleteModal)
    <div class="modal-backdrop" wire:click.self="$set('showDeleteModal', false)">
        <div class="modal-content p-5 text-center">
            <i data-lucide="alert-triangle" class="w-12 h-12 text-danger mx-auto mb-3"></i>
            <h3 class="text-lg font-semibold mb-2">Hapus Periode?</h3>
            <div class="flex gap-3 justify-center mt-6">
                <button wire:click="deletePeriode()" class="btn btn-danger">Hapus</button>
                <button wire:click="$set('showDeleteModal', false)" class="btn btn-ghost">Batal</button>
            </div>
        </div>
    </div>
    @endif
</div>
