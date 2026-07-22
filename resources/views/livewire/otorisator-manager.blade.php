<div class="space-y-6">
    <x-toast />

    <div class="page-header">
        <div>
            <h1 class="page-title">Otorisator</h1>
            <p class="page-subtitle">Kelola daftar otorisator yang berwenang</p>
        </div>
        @if($this->canAccess('master-data'))
        <button wire:click="create()" class="btn btn-primary">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Otorisator
        </button>
        @endif
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Otorisator</th>
                    <th>Jabatan</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($otorisators as $i => $o)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="font-medium">{{ $o->nama_otorisator }}</td>
                    <td>{{ $o->jabatan_otorisator }}</td>
                    <td class="text-right space-x-1">
                        @if($this->canAccess('master-data'))
                        <button wire:click="edit({{ $o->id }})" class="btn btn-ghost btn-sm"><i data-lucide="pencil" class="w-4 h-4"></i></button>
                        <button wire:click="confirmDelete({{ $o->id }})" class="btn btn-danger btn-sm"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center py-8 text-gray-500">Belum ada otorisator</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($showModal)
    <div class="modal-backdrop" wire:click.self="$set('showModal', false)">
        <div class="modal-content">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="text-lg font-semibold">{{ $editing ? 'Edit' : 'Tambah' }} Otorisator</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form wire:submit="save()" class="p-5 space-y-4">
                <div><label class="label">Nama</label><input type="text" wire:model="formData.nama_otorisator" class="input-field" required></div>
                <div><label class="label">Jabatan</label><input type="text" wire:model="formData.jabatan_otorisator" class="input-field" required></div>
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
            <h3 class="text-lg font-semibold">Hapus Otorisator?</h3>
            <div class="flex gap-3 justify-center mt-6">
                <button wire:click="deleteOtorisator()" class="btn btn-danger">Hapus</button>
                <button wire:click="$set('showDeleteModal', false)" class="btn btn-ghost">Batal</button>
            </div>
        </div>
    </div>
    @endif
</div>
