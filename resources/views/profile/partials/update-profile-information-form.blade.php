<div class="card">
    <div class="card-header">
        <h3 class="text-sm font-bold text-gray-900">{{ __('Informasi Profil') }}</h3>
    </div>
    <div class="card-body">
        <p class="text-xs text-gray-500 mb-4">{{ __("Perbarui informasi profil dan alamat email akun Anda.") }}</p>

        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')

            <div class="mb-4">
                <label for="image" class="label">{{ __('Foto Profil') }}</label>
                <div class="mb-3">
                    <img src="{{ asset('storage/' . $user->image) }}" alt="Profile Image" class="w-20 h-20 rounded-full border-4 border-gray-100 object-cover shadow-sm">
                </div>
                <input type="file" id="image" name="image" accept="image/*" class="input-field @error('image') border-danger @enderror">
                @error('image')
                <p class="text-xs text-danger mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="name" class="label">{{ __('Nama') }}</label>
                    <input id="name" name="name" type="text" class="input-field @error('name') border-danger @enderror" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                    @error('name')
                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="label">{{ __('Email') }}</label>
                    <input id="email" name="email" type="email" class="input-field @error('email') border-danger @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username">
                    @error('email')
                    <p class="text-xs text-danger mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="p-3 rounded-lg bg-warning-50 border border-warning-100 mb-4">
                <p class="text-xs text-amber-700">
                    {{ __('Alamat email Anda belum diverifikasi.') }}
                    <button form="send-verification" class="underline font-semibold hover:text-amber-800 transition-colors">
                        {{ __('Klik di sini untuk mengirim ulang email verifikasi.') }}
                    </button>
                </p>
                @if (session('status') === 'verification-link-sent')
                <p class="text-xs text-success mt-1">
                    {{ __('Tautan verifikasi baru telah dikirim ke alamat email Anda.') }}
                </p>
                @endif
            </div>
            @endif

            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary">{{ __('Simpan') }}</button>

                @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="text-xs text-success font-medium">
                    {{ __('Tersimpan.') }}
                </p>
                @endif
            </div>
        </form>
    </div>
</div>
