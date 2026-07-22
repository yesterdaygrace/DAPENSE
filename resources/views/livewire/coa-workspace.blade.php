@php
    $allHeaders = \App\Models\HeaderCOA::orderBy('kode_header')->get();
@endphp

<div class="space-y-6">
    <x-toast />

    <div class="page-header">
        <div>
            <h1 class="page-title">Ruang Kerja COA</h1>
            <p class="page-subtitle">Kelola struktur dan konfigurasi kode akun</p>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="flex border-b border-gray-200 mb-6 overflow-x-auto">
        @foreach([
            'accounts' => 'Akun',
            'headers' => 'Header',
            'mapping' => 'Pemetaan Header',
            'import' => 'Impor',
            'export' => 'Ekspor',
            'audit' => 'Riwayat Audit',
        ] as $key => $label)
        <button
            wire:click="switchTab('{{ $key }}')"
            class="px-6 py-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap {{ $activeTab === $key ? 'border-primary text-primary' : 'text-gray-500 hover:text-gray-700' }}"
        >
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- Accounts Tab --}}
    @if($activeTab === 'accounts')
    <div class="space-y-6">
        <div class="filter-card p-6">
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 max-w-md">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari akun..." class="input-field pl-10">
                </div>
                @if($this->canAccess('master-data'))
                <button wire:click="createAccount()" class="btn btn-primary">
                    <i data-lucide="plus" class="w-4 h-4"></i> Buat Akun
                </button>
                @endif
            </div>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Saldo Normal</th>
                        <th>Header</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coas as $coa)
                    <tr>
                        <td class="font-medium">{{ $coa->kode_akun }}</td>
                        <td>{{ $coa->nama_akun }}</td>
                        <td><span class="badge badge-primary">{{ ucfirst($coa->kategori) }}</span></td>
                        <td class="capitalize">{{ $coa->saldo_normal }}</td>
                        <td>{{ $coa->headerCoa->nama_header ?? '—' }}</td>
                        <td>
                            @if($coa->status ?? 'active' === 'active')
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-neutral">Tidak Aktif</span>
                            @endif
                        </td>
                        <td class="text-right space-x-2">
                            @if($this->canAccess('master-data'))
                            <button wire:click="editAccount({{ $coa->id }})" class="btn btn-ghost btn-sm">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </button>
                            <button wire:click="confirmDeleteAccount({{ $coa->id }})" class="btn btn-danger btn-sm">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-8 text-gray-500">Belum ada data akun</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $coas->links() }}</div>
    </div>
    @endif

    {{-- Headers Tab --}}
    @if($activeTab === 'headers')
    <div class="space-y-6">
        <div class="filter-card p-6">
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 max-w-md">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari header..." class="input-field pl-10">
                </div>
                @if($this->canAccess('master-data'))
                <button wire:click="createHeader()" class="btn btn-primary">
                    <i data-lucide="plus" class="w-4 h-4"></i> Buat Header
                </button>
                @endif
            </div>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Level</th>
                        <th>Induk</th>
                        <th>Akun</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($headers as $header)
                    <tr>
                        <td class="font-medium">{{ $header->kode_header }}</td>
                        <td>{{ $header->nama_header }}</td>
                        <td>Level {{ $header->level }}</td>
                        <td>{{ $header->parent->nama_header ?? '—' }}</td>
                        <td>{{ $header->coas_count ?? 0 }}</td>
                        <td class="text-right space-x-2">
                            @if($this->canAccess('master-data'))
                            <button wire:click="editHeader({{ $header->id }})" class="btn btn-ghost btn-sm">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </button>
                            <button wire:click="confirmDeleteHeader({{ $header->id }})" class="btn btn-danger btn-sm">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-8 text-gray-500">Belum ada data header</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $headers->links() }}</div>
    </div>
    @endif

    {{-- Mapping Tab --}}
    @if($activeTab === 'mapping')
    <div class="space-y-6">
        <div class="card">
            <div class="card-header">
                <h2 class="text-lg font-semibold">Pemetaan Header</h2>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nama Header</th>
                                <th>Akun yang Ditugaskan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($headerMappings as $header)
                            <tr>
                                <td class="font-medium">{{ $header->nama_header }}</td>
                                <td>
                                    @foreach($header->coas->take(5) as $coa)
                                        <span class="badge badge-neutral">{{ $coa->kode_akun }}</span>
                                    @endforeach
                                    @if($header->coas->count() > 5)
                                        <span class="text-gray-500 text-xs">+{{ $header->coas->count() - 5 }} lainnya</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center py-8 text-gray-500">Belum ada data header</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Import Tab --}}
    @if($activeTab === 'import')
    <div class="space-y-6">
        <div class="card">
            <div class="card-body">
                <form action="{{ route($this->routePrefix() . '.coa-workspace.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="text-center py-12 border-2 border-dashed border-gray-300 rounded-[16px]">
                        <i data-lucide="upload" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Seret & Jatuhkan File atau Klik untuk Menelusuri</h3>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" class="hidden" id="import-file" required>
                        <label for="import-file" class="btn btn-primary mt-4 cursor-pointer">
                            <i data-lucide="folder" class="w-4 h-4"></i> Jelajahi File
                        </label>
                    </div>
                    <div class="mt-4 flex gap-3">
                        <button type="submit" class="btn btn-primary"><i data-lucide="upload" class="w-4 h-4"></i> Impor Data</button>
                        <a href="{{ route($this->routePrefix() . '.coa-workspace.template') }}" class="btn btn-ghost"><i data-lucide="download" class="w-4 h-4"></i> Unduh Template</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Export Tab --}}
    @if($activeTab === 'export')
    <div class="space-y-6">
        <div class="card">
            <div class="card-header"><h2 class="text-lg font-semibold">Ekspor Kode Akun</h2></div>
            <div class="card-body">
                <form action="{{ route($this->routePrefix() . '.coa-workspace.export') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="label">Format Ekspor</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2"><input type="radio" name="format" value="excel" checked class="text-primary"> Excel (.xlsx)</label>
                                <label class="flex items-center gap-2"><input type="radio" name="format" value="csv" class="text-primary"> CSV (.csv)</label>
                            </div>
                        </div>
                        <div>
                            <label class="label">Sertakan</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2"><input type="checkbox" name="include_headers" value="1" checked class="text-primary"> Penugasan header</label>
                                <label class="flex items-center gap-2"><input type="checkbox" name="include_audit" value="1" class="text-primary"> Informasi audit</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary"><i data-lucide="download" class="w-4 h-4"></i> Ekspor Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Audit Tab --}}
    @if($activeTab === 'audit')
    <div class="card">
        <div class="card-header"><h2 class="text-lg font-semibold">Riwayat Audit</h2></div>
        <div class="card-body text-center py-12 text-gray-500">
            <i data-lucide="history" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
            <p class="font-medium">Audit logging belum diaktifkan</p>
            <p class="text-sm mt-1">Catatan aktivitas akan muncul di sini setelah diaktifkan.</p>
        </div>
    </div>
    @endif

    {{-- Account Create/Edit Modal --}}
    @if($showModal)
    <div class="modal-backdrop" wire:click.self="$set('showModal', false)">
        <div class="modal-content">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="text-lg font-semibold">{{ $editing ? 'Edit Akun' : 'Buat Akun' }}</h3>
                <button wire:click="$set('showModal', false)" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form wire:submit="saveAccount()" class="p-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="label">Kode Akun</label><input type="text" wire:model="formData.kode_akun" class="input-field" placeholder="100-001"></div>
                    <div><label class="label">Nama Akun</label><input type="text" wire:model="formData.nama_akun" class="input-field" placeholder="KAS"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="label">Kategori</label>
                        <select wire:model="formData.kategori" class="select-field">
                            <option value="">Pilih Kategori</option>
                            <option value="asset">Asset</option>
                            <option value="liability">Liability</option>
                            <option value="equity">Equity</option>
                            <option value="revenue">Revenue</option>
                            <option value="expense">Expense</option>
                        </select>
                    </div>
                    <div><label class="label">Saldo Normal</label>
                        <select wire:model="formData.saldo_normal" class="select-field">
                            <option value="Debit">Debit</option>
                            <option value="Kredit">Kredit</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="label">Level</label><input type="number" wire:model="formData.level" class="input-field" min="1"></div>
                    <div><label class="label">Header</label>
                        <select wire:model="formData.header_coa_id" class="select-field">
                            <option value="">Pilih Header</option>
                            @foreach($allHeaders as $h)
                                <option value="{{ $h->id }}">{{ $h->kode_header }} — {{ $h->nama_header }}</option>
                            @endforeach
                        </select>
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

    {{-- Header Create/Edit Modal --}}
    @if($showHeaderModal)
    <div class="modal-backdrop" wire:click.self="$set('showHeaderModal', false)">
        <div class="modal-content">
            <div class="flex items-center justify-between p-5 border-b border-gray-100">
                <h3 class="text-lg font-semibold">{{ $editingHeader ? 'Edit Header' : 'Buat Header' }}</h3>
                <button wire:click="$set('showHeaderModal', false)" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form wire:submit="saveHeader()" class="p-5 space-y-4">
                <div><label class="label">Kode Header</label><input type="text" wire:model="headerForm.kode_header" class="input-field"></div>
                <div><label class="label">Nama Header</label><input type="text" wire:model="headerForm.nama_header" class="input-field"></div>
                <div><label class="label">Level</label><input type="number" wire:model="headerForm.level" class="input-field" min="1"></div>
                <div><label class="label">Header Induk</label>
                    <select wire:model="headerForm.parent_id" class="select-field">
                        <option value="">Tidak Ada</option>
                        @foreach($allHeaders as $h)
                            <option value="{{ $h->id }}">{{ $h->kode_header }} — {{ $h->nama_header }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" class="btn btn-primary"><i data-lucide="check" class="w-4 h-4"></i> Simpan</button>
                    <button type="button" wire:click="$set('showHeaderModal', false)" class="btn btn-ghost">Batal</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Delete Account Confirmation --}}
    @if($showDeleteModal)
    <x-delete-modal
        wire:click="deleteAccount()"
        wire:click.self="$set('showDeleteModal', false)"
        title="Hapus Akun"
        message="Apakah Anda yakin ingin menghapus akun ini? Tindakan ini tidak dapat dibatalkan."
    />
    @endif

    {{-- Delete Header Confirmation --}}
    @if($showHeaderDeleteModal)
    <x-delete-modal
        wire:click="deleteHeader()"
        wire:click.self="$set('showHeaderDeleteModal', false)"
        title="Hapus Header"
        message="Apakah Anda yakin ingin menghapus header ini?"
    />
    @endif
</div>
