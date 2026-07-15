<div class="card border-danger/20">
    <div class="card-header">
        <h3 class="text-sm font-bold text-danger">{{ __('Hapus Akun') }}</h3>
    </div>
    <div class="card-body">
        <p class="text-xs text-gray-500 mb-4">{{ __('Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Sebelum menghapus akun, silakan unduh data atau informasi yang ingin Anda pertahankan.') }}</p>

        <button
            type="button"
            class="btn-danger"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >{{ __('Hapus Akun') }}</button>

        <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
            <div class="p-6">
                <h2 class="text-lg font-bold text-gray-900">
                    {{ __('Anda yakin ingin menghapus akun?') }}
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    {{ __('Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen. Masukkan kata sandi Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun secara permanen.') }}
                </p>

                <div class="mt-6">
                    <label for="password" class="label sr-only">{{ __('Kata Sandi') }}</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="input-field w-3/4"
                        placeholder="{{ __('Kata Sandi') }}"
                        autocomplete="current-password"
                    >
                    @error('password')
                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" class="btn-secondary" x-on:click="$dispatch('close')">
                        {{ __('Batal') }}
                    </button>

                    <form method="post" action="{{ route('profile.destroy') }}">
                        @csrf
                        @method('delete')
                        <button type="submit" class="btn-danger">
                            {{ __('Hapus Akun') }}
                        </button>
                    </form>
                </div>
            </div>
        </x-modal>
    </div>
</div>
