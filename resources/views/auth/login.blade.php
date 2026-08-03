<x-guest-layout>
<x-auth-card-layout>
    <div class="text-center mb-8">
        <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-primary-50">
            <img src="{{ asset('logo.svg') }}" alt="DAPENSE" class="w-8 h-8" />
        </div>
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Masuk</h1>
        <p class="mt-2 text-sm text-gray-500">Masukkan kredensial Anda untuk melanjutkan.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="label">Email</label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="email"
                class="input-field @error('email') border-danger @enderror"
                placeholder="nama@perusahaan.com"
            />
            @error('email')
            <p class="text-xs text-danger mt-1.5" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div x-data="{ show: false }">
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="label mb-0">Kata Sandi</label>
                @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-primary hover:text-primary-700 transition-colors">Lupa kata sandi?</a>
                @endif
            </div>
            <div class="relative">
                <input
                    id="password"
                    :type="show ? 'text' : 'password'"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="input-field pr-11 @error('password') border-danger @enderror"
                    placeholder="Masukkan kata sandi"
                />
                <button
                    type="button"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
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
            <p class="text-xs text-danger mt-1.5" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center">
            <input
                id="remember_me"
                type="checkbox"
                name="remember"
                class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary/20"
            />
            <label for="remember_me" class="ml-2.5 text-sm text-gray-600">Ingat saya</label>
        </div>

        <button type="submit" class="btn-primary w-full h-[52px]">
            Masuk
        </button>

        <div class="relative my-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="bg-white px-3 text-gray-400 font-medium">atau</span>
            </div>
        </div>

        <a href="{{ route('demo.login') }}"
           class="flex items-center justify-center gap-2 w-full h-[52px] rounded-[--radius-button] text-sm font-semibold
                  border-2 border-dashed border-primary-300 text-primary-600
                  hover:bg-primary-50 hover:border-primary-400
                  active:scale-[0.97] transition-all duration-200">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
            </svg>
            Mode Demo
        </a>
    </form>

    <p class="mt-8 text-center text-sm text-gray-500">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-semibold text-primary hover:text-primary-700 transition-colors">Daftar</a>
    </p>

    <p class="mt-4 text-center text-xs text-gray-400">
        Dengan melanjutkan, Anda menyetujui
        <a href="#" class="underline hover:text-gray-500 transition-colors">Ketentuan Layanan</a>
        dan
        <a href="#" class="underline hover:text-gray-500 transition-colors">Kebijakan Privasi</a>
    </p>
</x-auth-card-layout>
</x-guest-layout>
