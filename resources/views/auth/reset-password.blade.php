<x-guest-layout>
<x-auth-card-layout>
    <div class="text-center mb-8">
        <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-primary-50">
            <svg class="h-7 w-7 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Atur Ulang Kata Sandi</h1>
        <p class="mt-2 text-sm text-gray-500">Masukkan kata sandi baru untuk akun Anda.</p>
    </div>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mb-4">
            <label for="email" class="label">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email', $request->email ?? '') }}"
                required
                autofocus
                class="input-field @error('email') border-danger @enderror"
            />
            @error('email')
            <p class="text-xs text-danger mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="label">Kata Sandi Baru</label>
            <input
                id="password"
                type="password"
                name="password"
                required
                class="input-field @error('password') border-danger @enderror"
                autocomplete="new-password"
            />
            @error('password')
            <p class="text-xs text-danger mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="password_confirmation" class="label">Konfirmasi Kata Sandi</label>
            <input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                class="input-field"
                autocomplete="new-password"
            />
            @error('password_confirmation')
            <p class="text-xs text-danger mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-primary w-full h-[52px]">
            Reset Kata Sandi
        </button>
    </form>
</x-auth-card-layout>
</x-guest-layout>
