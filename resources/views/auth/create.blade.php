<x-guest-layout>
    <h2 class="mb-4 text-xl font-semibold tracking-tight">Tambah periode</h2>

    @if (session()->has('error'))
    <div class="px-3 py-2 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">{{ session('error') }}</div>
    @endif

    <form action="{{ route('login/periode/save') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="nama_periode" class="block text-sm font-medium text-gray-700">Nama periode</label>
            <input type="text" id="nama_periode" name="nama_periode"
                class="block w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-[#1E3A8A] focus:ring-[#1E3A8A] sm:text-sm @error('nama_periode') border-red-300 @enderror"
                value="{{ old('nama_periode') }}">
            @error('nama_periode')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="mb-3">
            <label for="tanggal_awal" class="block text-sm font-medium text-gray-700">Tanggal awal</label>
            <input type="date" id="tanggal_awal" name="tanggal_awal"
                class="block w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-[#1E3A8A] focus:ring-[#1E3A8A] sm:text-sm @error('tanggal_awal') border-red-300 @enderror"
                value="{{ old('tanggal_awal') }}">
            @error('tanggal_awal')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="mb-3">
            <label for="tanggal_akhir" class="block text-sm font-medium text-gray-700">Tanggal akhir</label>
            <input type="date" id="tanggal_akhir" name="tanggal_akhir"
                class="block w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-[#1E3A8A] focus:ring-[#1E3A8A] sm:text-sm @error('tanggal_akhir') border-red-300 @enderror"
                value="{{ old('tanggal_akhir') }}">
            @error('tanggal_akhir')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="flex items-center justify-between mt-4">
            <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Kembali</a>
            <button type="submit" class="rounded-lg bg-[#1E3A8A] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-[#15255A] focus:outline-none focus:ring-2 focus:ring-[#1E3A8A] focus:ring-offset-2 transition-all active:scale-[0.98]">Simpan</button>
        </div>
    </form>
</x-guest-layout>
