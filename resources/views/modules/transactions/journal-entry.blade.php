@extends('layouts.applayout')

@section('title', 'Entri Jurnal')

@section('content')
@php
    $prefix = match(Auth::user()->usertype) {
        'rootsuperuser' => '/rootsuperuser',
        'admin' => '/admin',
        'operator' => '/operator',
        'bod' => '/bod',
        default => ''
    };
@endphp

<div class="space-y-6" x-data="{
    transactionType: 'km',
    entries: [{ coa_id: '', keterangan: '', debit: 0, kredit: 0 }],
    coas: {{ Js::from(\App\Models\COA::all()->map(fn($c) => ['id' => $c->id, 'kode' => $c->kode_akun, 'nama' => $c->nama_akun])) }},

    get totalDebit() { return this.entries.reduce((sum, e) => sum + (parseFloat(e.debit) || 0), 0); },
    get totalKredit() { return this.entries.reduce((sum, e) => sum + (parseFloat(e.kredit) || 0), 0); },
    get isBalanced() { return this.totalDebit === this.totalKredit && this.totalDebit > 0; },

    addEntry() { this.entries.push({ coa_id: '', keterangan: '', debit: 0, kredit: 0 }); },
    removeEntry(i) { if (this.entries.length > 1) this.entries.splice(i, 1); },

    get jenisLabel() {
        return {
            km: 'Kas Masuk',
            kk: 'Kas Keluar',
            bm: 'Bank Masuk',
            bk: 'Bank Keluar',
            mem: 'Memorial',
            mempenutup: 'Memorial Penutup'
        }[this.transactionType] || '';
    }
}">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm text-gray-600">
        <a href="{{ $prefix }}/dashboard" class="hover:text-primary transition-colors">Dasbor</a>
        <i class='bx bx-chevron-right text-gray-400'></i>
        <a href="{{ $prefix }}/transactions" class="hover:text-primary transition-colors">Transaksi</a>
        <i class='bx bx-chevron-right text-gray-400'></i>
        <span class="text-gray-900 font-medium">Entri Jurnal</span>
    </nav>

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Entri Jurnal</h1>
            <p class="mt-2 text-gray-600">Buat entri jurnal baru untuk jenis transaksi apapun</p>
        </div>
        <div class="text-right">
            <span class="inline-flex items-center gap-2 px-4 py-2 bg-primary-50 text-primary-700 rounded-full text-sm font-semibold">
                <i class="bx bx-edit text-lg"></i>
                <span x-text="jenisLabel"></span>
            </span>
        </div>
    </div>

    <form method="POST" action="{{ $prefix }}/transactions/journal-entry">
        @csrf
        <div class="bg-white rounded-[--radius-card] shadow-card p-6 space-y-6">
            <!-- Row 1: Type + Date + Reference -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Transaksi</label>
                    <select name="kategori_jurnal" x-model="transactionType" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                        <option value="km">Kas Masuk (KM)</option>
                        <option value="kk">Kas Keluar (KK)</option>
                        <option value="bm">Bank Masuk (BM)</option>
                        <option value="bk">Bank Keluar (BK)</option>
                        <option value="mem">Memorial (MEM)</option>
                        <option value="mempenutup">Memorial Penutup</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal_jurnal" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Bukti</label>
                    <input type="text" name="nomor_bukti" required placeholder="Otomatis" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm bg-gray-50 focus:ring-2 focus:ring-primary focus:border-primary" />
                </div>
            </div>

            <!-- Account Lines -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-900">Baris Akun</h3>
                    <button type="button" @click="addEntry()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-primary bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors">
                        <i class="bx bx-plus text-base"></i>
                        Tambah Baris
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
                            <template x-for="(entry, i) in entries" :key="i">
                                <tr class="border-b border-gray-100">
                                    <td class="py-2">
                                        <select :name="'coa_id[]'" x-model="entry.coa_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary">
                                            <option value="">Pilih Akun</option>
                                            <template x-for="coa in coas" :key="coa.id">
                                                <option :value="coa.id" x-text="coa.kode + ' — ' + coa.nama"></option>
                                            </template>
                                        </select>
                                    </td>
                                    <td class="py-2">
                                        <input type="text" :name="'keterangan[]'" x-model="entry.keterangan" placeholder="Keterangan" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary focus:border-primary" />
                                    </td>
                                    <td class="py-2">
                                        <input type="number" :name="'debit[]'" x-model.number="entry.debit" step="0.01" min="0" placeholder="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-right focus:ring-2 focus:ring-primary focus:border-primary" />
                                    </td>
                                    <td class="py-2">
                                        <input type="number" :name="'kredit[]'" x-model.number="entry.kredit" step="0.01" min="0" placeholder="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-right focus:ring-2 focus:ring-primary focus:border-primary" />
                                    </td>
                                    <td class="py-2 text-center">
                                        <button type="button" @click="removeEntry(i)" x-show="entries.length > 1" class="p-1 text-gray-400 hover:text-red-500 transition-colors">
                                            <i class="bx bx-trash text-lg"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-gray-300 font-semibold">
                                <td colspan="2" class="py-3 text-right text-gray-700">Total</td>
                                <td class="py-3 text-right" :class="totalDebit > 0 ? 'text-gray-900' : 'text-gray-400'" x-text="'Rp ' + totalDebit.toLocaleString('id-ID')"></td>
                                <td class="py-3 text-right" :class="totalKredit > 0 ? 'text-gray-900' : 'text-gray-400'" x-text="'Rp ' + totalKredit.toLocaleString('id-ID')"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Balance Indicator -->
                <div class="mt-3 flex items-center gap-2">
                    <template x-if="isBalanced">
                        <span class="inline-flex items-center gap-1.5 text-sm text-emerald-600 font-medium">
                            <i class="bx bx-check-circle"></i>
                            Seimbang
                        </span>
                    </template>
                    <template x-if="!isBalanced">
                        <span class="inline-flex items-center gap-1.5 text-sm text-red-600 font-medium">
                            <i class="bx bx-error-circle"></i>
                            <span x-text="'Selisih: Rp ' + Math.abs(totalDebit - totalKredit).toLocaleString('id-ID')"></span>
                        </span>
                    </template>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="px-6 py-2.5 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" :disabled="!isBalanced">
                    <i class="bx bx-check mr-1"></i>
                    Posting Jurnal
                </button>
                <button type="button" class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="bx bx-save mr-1"></i>
                    Simpan Draft
                </button>
                <a href="{{ $prefix }}/transactions" class="px-6 py-2.5 text-gray-500 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
