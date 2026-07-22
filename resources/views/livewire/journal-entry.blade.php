<div class="space-y-6">
    <x-toast />

    <div class="page-header">
        <div>
            <h1 class="page-title">Entri Jurnal</h1>
            <p class="page-subtitle">Buat entri jurnal baru untuk jenis transaksi apapun</p>
        </div>
        <span class="badge badge-primary">
            <i data-lucide="edit" class="w-4 h-4"></i>
            <span x-text="'{{ $this->jenisLabel }}'"></span>
        </span>
    </div>

    <form wire:submit="save()" class="card">
        <div class="card-body space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="label">Jenis Transaksi</label>
                    <select wire:model="transactionType" class="select-field">
                        <option value="km">Kas Masuk (KM)</option>
                        <option value="kk">Kas Keluar (KK)</option>
                        <option value="bm">Bank Masuk (BM)</option>
                        <option value="bk">Bank Keluar (BK)</option>
                        <option value="mem">Memorial (MEM)</option>
                        <option value="mempenutup">Memorial Penutup</option>
                    </select>
                </div>
                <div>
                    <label class="label">Tanggal</label>
                    <input type="date" wire:model="tanggalJurnal" class="input-field" required>
                </div>
                <div>
                    <label class="label">Nomor Bukti</label>
                    <input type="text" wire:model="nomorBukti" class="input-field" readonly>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold">Baris Akun</h3>
                    <button type="button" wire:click="addEntry()" class="btn btn-ghost btn-sm">
                        <i data-lucide="plus" class="w-4 h-4"></i> Tambah Baris
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 text-left">
                                <th class="pb-2 font-medium text-gray-500 w-2/5">Akun</th>
                                <th class="pb-2 font-medium text-gray-500 w-1/5">Keterangan</th>
                                <th class="pb-2 font-medium text-gray-500 w-1/5 text-right">Debit (Rp)</th>
                                <th class="pb-2 font-medium text-gray-500 w-1/5 text-right">Kredit (Rp)</th>
                                <th class="pb-2 w-10"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entries as $i => $entry)
                            <tr class="border-b border-gray-100">
                                <td class="py-2">
                                    <select wire:model="entries.{{ $i }}.coa_id" class="select-field" required>
                                        <option value="">Pilih Akun</option>
                                        @foreach($coas as $coa)
                                            <option value="{{ $coa['id'] }}">{{ $coa['kode'] }} — {{ $coa['nama'] }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="py-2">
                                    <input type="text" wire:model="entries.{{ $i }}.keterangan" class="input-field" placeholder="Keterangan">
                                </td>
                                <td class="py-2">
                                    <input type="number" wire:model="entries.{{ $i }}.debit" step="0.01" min="0" class="input-field text-right">
                                </td>
                                <td class="py-2">
                                    <input type="number" wire:model="entries.{{ $i }}.kredit" step="0.01" min="0" class="input-field text-right">
                                </td>
                                <td class="py-2 text-center">
                                    @if(count($entries) > 1)
                                    <button type="button" wire:click="removeEntry({{ $i }})" class="text-gray-400 hover:text-red-500">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-gray-300 font-semibold">
                                <td colspan="2" class="py-3 text-right">Total</td>
                                <td class="py-3 text-right tabular-nums">Rp {{ number_format($this->totalDebit, 0, ',', '.') }}</td>
                                <td class="py-3 text-right tabular-nums">Rp {{ number_format($this->totalKredit, 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-3">
                    @if($this->isBalanced)
                        <span class="badge badge-success"><i data-lucide="check-circle" class="w-4 h-4"></i> Seimbang</span>
                    @else
                        <span class="badge badge-danger">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            Selisih: Rp {{ number_format(abs($this->totalDebit - $this->totalKredit), 0, ',', '.') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 p-5 border-t border-gray-100">
            <button type="submit" class="btn btn-primary" @unless($this->isBalanced) disabled @endunless>
                <i data-lucide="check" class="w-4 h-4"></i> Posting Jurnal
            </button>
            <a href="{{ route($this->routePrefix() . '.transactions') }}" class="btn btn-ghost">Batal</a>
        </div>
    </form>
</div>
