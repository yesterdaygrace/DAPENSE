<x-guest-layout>
    <div class="mb-6 text-center">
        <div class="w-12 h-12 rounded-xl bg-primary-50 flex items-center justify-center mx-auto mb-3">
            <i data-lucide="calendar-plus" class="w-6 h-6 text-primary"></i>
        </div>
        <h2 class="text-xl font-bold text-gray-900 tracking-tight">Tambah Periode</h2>
        <p class="text-sm text-gray-500 mt-1">Buat periode akuntansi baru untuk memulai</p>
    </div>

    <form action="{{ route('login/periode/save') }}" method="POST">
        @csrf
        <div class="space-y-4">
            <div>
                <label for="nama_periode" class="label">Nama Periode</label>
                <input type="text" id="nama_periode" name="nama_periode"
                    class="input-field @error('nama_periode') border-danger @enderror"
                    value="{{ old('nama_periode') }}" placeholder="Contoh: 2024 - Januari">
                @error('nama_periode')<p class="text-sm text-danger mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="tanggal_awal" class="label">Tanggal Awal</label>
                <input type="date" id="tanggal_awal" name="tanggal_awal"
                    class="input-field @error('tanggal_awal') border-danger @enderror"
                    value="{{ old('tanggal_awal') }}">
                @error('tanggal_awal')<p class="text-sm text-danger mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="tanggal_akhir" class="label">Tanggal Akhir</label>
                <input type="date" id="tanggal_akhir" name="tanggal_akhir"
                    class="input-field @error('tanggal_akhir') border-danger @enderror"
                    value="{{ old('tanggal_akhir') }}">
                @error('tanggal_akhir')<p class="text-sm text-danger mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('login') }}" class="btn-ghost text-sm">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1 inline-block"></i>
                Kembali
            </a>
            <button type="submit" class="btn-primary">
                <i data-lucide="save" class="w-4 h-4 mr-1.5 inline-block"></i>
                Simpan
            </button>
        </div>
    </form>
</x-guest-layout>
