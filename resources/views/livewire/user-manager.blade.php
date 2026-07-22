<div class="space-y-6">
    <x-toast />

    <div class="page-header">
        <div>
            <h1 class="page-title">Manajemen Pengguna</h1>
            <p class="page-subtitle">Kelola akun dan peran pengguna sistem</p>
        </div>
        @if($this->canAccess('users'))
        <button wire:click="create()" class="btn btn-primary">
            <i data-lucide="plus" class="w-4 h-4"></i> Tambah Pengguna
        </button>
        @endif
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Peran</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td class="font-medium">{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @php
                            $roleBadge = match($user->usertype) {
                                'rootsuperuser' => 'badge-danger',
                                'admin' => 'badge-primary',
                                'operator' => 'badge-success',
                                'bod' => 'badge-warning',
                                default => 'badge-neutral',
                            };
                        @endphp
                        <span class="badge {{ $roleBadge }}">{{ ucfirst($user->usertype) }}</span>
                    </td>
                    <td>
                        @if($user->status)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-neutral">Nonaktif</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                    <td class="text-right space-x-1">
                        @if($this->canAccess('users'))
                        <button wire:click="edit({{ $user->id }})" class="btn btn-ghost btn-sm"><i data-lucide="pencil" class="w-4 h-4"></i></button>
                        <button wire:click="confirmDelete({{ $user->id }})" class="btn btn-danger btn-sm"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-gray-500">Belum ada pengguna</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($showModal)
    <div class="modal-backdrop" wire:click.self="$set('showModal', false)">
        <div class="modal-content">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="text-lg font-semibold">{{ $editing ? 'Edit' : 'Tambah' }} Pengguna</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form wire:submit="save()" class="p-5 space-y-4">
                <div><label class="label">Nama</label><input type="text" wire:model="formData.name" class="input-field" required></div>
                <div><label class="label">Email</label><input type="email" wire:model="formData.email" class="input-field" required></div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="label">Peran</label>
                        <select wire:model="formData.usertype" class="select-field" required>
                            <option value="operator">Operator</option>
                            <option value="admin">Admin</option>
                            <option value="bod">BOD</option>
                            <option value="rootsuperuser">Root Superuser</option>
                        </select>
                    </div>
                    <div>
                        <label class="label">Status</label>
                        <select wire:model="formData.status" class="select-field" required>
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="label">{{ $editing ? 'Password (biarkan kosong jika tidak ingin ubah)' : 'Password' }}</label>
                    <input type="password" wire:model="formData.password" class="input-field" {{ $editing ? '' : 'required' }}>
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
            <h3 class="text-lg font-semibold">Hapus Pengguna?</h3>
            <p class="text-gray-600 text-sm">Data akan dihapus permanen.</p>
            <div class="flex gap-3 justify-center mt-6">
                <button wire:click="deleteUser()" class="btn btn-danger">Hapus</button>
                <button wire:click="$set('showDeleteModal', false)" class="btn btn-ghost">Batal</button>
            </div>
        </div>
    </div>
    @endif
</div>
