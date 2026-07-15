<div class="card">
    <div class="card-header">
        <h3 class="text-sm font-bold text-gray-900">{{ __('Perbarui Kata Sandi') }}</h3>
    </div>
    <div class="card-body">
        <p class="text-xs text-gray-500 mb-4">{{ __('Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.') }}</p>

        <form method="post" action="{{ route('password.update') }}">
            @csrf
            @method('put')

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                <div>
                    <label for="update_password_current_password" class="label">{{ __('Kata Sandi Saat Ini') }}</label>
                    <input id="update_password_current_password" name="current_password" type="password" class="input-field @error('current_password') border-danger @enderror" autocomplete="current-password">
                    @error('current_password')
                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="update_password_password" class="label">{{ __('Kata Sandi Baru') }}</label>
                    <input id="update_password_password" name="password" type="password" class="input-field @error('password') border-danger @enderror" autocomplete="new-password">
                    @error('password')
                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="update_password_password_confirmation" class="label">{{ __('Konfirmasi Kata Sandi') }}</label>
                    <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="input-field" autocomplete="new-password">
                    @error('password_confirmation')
                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary">{{ __('Simpan') }}</button>

                @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-xs text-success font-medium">
                    {{ __('Tersimpan.') }}
                </p>
                @endif
            </div>
        </form>
    </div>
</div>
