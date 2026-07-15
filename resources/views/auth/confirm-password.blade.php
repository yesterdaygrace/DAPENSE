<x-guest-layout>
<x-auth-card-layout>
    <div class="text-center mb-8">
        <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-warning-50">
            <svg class="h-7 w-7 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Konfirmasi Kata Sandi</h1>
        <p class="mt-2 text-sm text-gray-500">Ini adalah area aman dari aplikasi. Silakan konfirmasi kata sandi Anda sebelum melanjutkan.</p>
    </div>

    @if (session('status'))
    <div class="p-3 rounded-lg bg-success-50 border border-success-100 mb-6">
        <p class="text-xs text-success font-medium">{{ session('status') }}</p>
    </div>
    @endif

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="mb-6">
            <label for="password" class="label">Kata Sandi</label>
            <input
                id="password"
                type="password"
                name="password"
                class="input-field @error('password') border-danger @enderror"
                autofocus
                autocomplete="current-password"
                placeholder="Masukkan kata sandi Anda"
            />
            @error('password')
            <p class="text-xs text-danger mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-primary w-full h-[52px]">
            Konfirmasi
        </button>
    </form>
</x-auth-card-layout>
</x-guest-layout>
