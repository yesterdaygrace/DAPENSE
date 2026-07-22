<div class="space-y-6">
    <x-toast />

    @if(!$this->canAccess('posting'))
    <div class="card">
        <div class="card-body text-center py-12">
            <i data-lucide="shield-off" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
            <p class="text-gray-600">Anda tidak memiliki akses ke halaman ini.</p>
        </div>
    </div>
    @else
    <div class="page-header">
        <div>
            <h1 class="page-title">Posting Jurnal</h1>
            <p class="page-subtitle">Hitung neraca saldo periode tertentu</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="text-lg font-semibold">Pilih Periode</h2>
        </div>
        <div class="card-body space-y-4">
            <div>
                <label class="label">Periode</label>
                <select wire:model="periodeId" class="select-field">
                    <option value="">— Pilih Periode —</option>
                    @foreach($periodes as $p)
                        <option value="{{ $p->id }}">{{ $p->nama_periode }} {{ $p->is_rekap ? '(Sudah Direkap)' : '(Aktif)' }}</option>
                    @endforeach
                </select>
            </div>

            @if($periodeId && $periodeStatus)
            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                <div class="flex-1">
                    <p class="text-sm text-gray-600">Jurnal dalam periode ini:</p>
                    <p class="text-xl font-bold text-gray-900">{{ $entryCount }}</p>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-600">Status:</p>
                    @if($periodeStatus->is_rekap)
                        <span class="badge badge-success"><i data-lucide="check-circle" class="w-4 h-4"></i> Sudah Direkap</span>
                    @else
                        <span class="badge badge-warning"><i data-lucide="clock" class="w-4 h-4"></i> Aktif (Belum Direkap)</span>
                    @endif
                </div>
            </div>

            <div class="flex gap-3">
                @if(!$periodeStatus->is_rekap && $entryCount > 0)
                <button wire:click="showPost()" class="btn btn-primary">
                    <i data-lucide="check" class="w-4 h-4"></i> Posting Jurnal
                </button>
                @endif

                @if($periodeStatus->is_rekap)
                <button wire:click="showUnpost()" class="btn btn-danger">
                    <i data-lucide="undo" class="w-4 h-4"></i> Unpost Jurnal
                </button>
                @endif
            </div>
            @endif
        </div>
    </div>

    @if($showConfirmModal)
    <div class="modal-backdrop" wire:click.self="$set('showConfirmModal', false)">
        <div class="modal-content p-5 text-center">
            <i data-lucide="alert-triangle" class="w-12 h-12 text-warning mx-auto mb-3"></i>
            <h3 class="text-lg font-semibold mb-2">
                {{ $postingAction === 'post' ? 'Posting Neraca Saldo?' : 'Unpost Neraca Saldo?' }}
            </h3>
            <p class="text-gray-600 text-sm">
                {{ $postingAction === 'post' ? 'Hitung total debit/kredit per akun dan simpan ke neraca saldo.' : 'Hapus semua data neraca saldo untuk periode ini.' }}
            </p>
            <div class="flex gap-3 justify-center mt-6">
                <button wire:click="executeAction()" class="{{ $postingAction === 'post' ? 'btn btn-primary' : 'btn btn-danger' }}">
                    {{ $postingAction === 'post' ? 'Ya, Posting' : 'Ya, Unpost' }}
                </button>
                <button wire:click="$set('showConfirmModal', false)" class="btn btn-ghost">Batal</button>
            </div>
        </div>
    </div>
    @endif
    @endif
</div>
