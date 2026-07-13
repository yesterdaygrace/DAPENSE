<x-guest-layout>
<x-auth-card-layout>
    {{-- Header --}}
    <div class="text-center mb-8">
        <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-[#EEF2FF]">
            <svg class="h-7 w-7 text-[#1E3A8A]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-[#0F172A] tracking-tight">Masuk</h1>
        <p class="mt-2 text-sm text-[#64748B]">Masukkan kredensial Anda untuk melanjutkan.</p>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-[#334155] mb-1.5">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="email"
                class="block w-full rounded-xl border border-[#E2E8F0] bg-white px-4 py-3 text-sm text-[#0F172A] placeholder:text-[#64748B] shadow-sm transition-colors focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20"
                placeholder="nama@perusahaan.com"
                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                aria-describedby="{{ $errors->has('email') ? 'email-error' : '' }}"
            />
            @error('email')
                <p id="email-error" class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div x-data="{ show: false }">
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-sm font-medium text-[#334155]">Kata Sandi</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-medium text-[#2563EB] hover:text-[#1E3A8A] transition-colors focus:outline-none focus:underline">Lupa kata sandi?</a>
                @endif
            </div>
            <div class="relative">
                <input
                    id="password"
                    :type="show ? 'text' : 'password'"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="block w-full rounded-xl border border-[#E2E8F0] bg-white px-4 py-3 text-sm text-[#0F172A] placeholder:text-[#64748B] shadow-sm transition-colors focus:border-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#2563EB]/20 pr-11"
                    placeholder="Masukkan kata sandi"
                    aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                    aria-describedby="{{ $errors->has('password') ? 'password-error' : '' }}"
                />
                <button
                    type="button"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-[#64748B] hover:text-[#334155] transition-colors focus:outline-none focus:text-[#1E3A8A]"
                    @click="show = !show"
                    :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                    aria-controls="password"
                >
                    <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <svg x-show="show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                </button>
            </div>
            @error('password')
                <p id="password-error" class="mt-1.5 text-sm text-red-600" role="alert">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center">
            <input
                id="remember_me"
                type="checkbox"
                name="remember"
                class="h-4 w-4 rounded border-[#E2E8F0] text-[#1E3A8A] shadow-sm focus:ring-[#2563EB]/20 focus:ring-2"
            />
            <label for="remember_me" class="ml-2.5 text-sm text-[#334155]">Ingat saya</label>
        </div>

        {{-- Submit Button --}}
        <button
            type="submit"
            class="flex w-full items-center justify-center rounded-xl bg-[#1E3A8A] px-4 py-3.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-[#15255A] focus:outline-none focus:ring-2 focus:ring-[#1E3A8A] focus:ring-offset-2 active:scale-[0.98] h-[52px]"
        >
            Masuk
        </button>
    </form>

    {{-- Register Link --}}
    <p class="mt-8 text-center text-sm text-[#64748B]">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-medium text-[#2563EB] hover:text-[#1E3A8A] transition-colors focus:outline-none focus:underline">Daftar</a>
    </p>

    {{-- Privacy Notice --}}
    <p class="mt-4 text-center text-xs text-[#94A3B8]">
        Dengan melanjutkan, Anda menyetujui
        <a href="#" class="underline hover:text-[#64748B] transition-colors focus:outline-none focus:underline">Ketentuan Layanan</a>
        dan
        <a href="#" class="underline hover:text-[#64748B] transition-colors focus:outline-none focus:underline">Kebijakan Privasi</a>
    </p>
</x-auth-card-layout>
</x-guest-layout>
