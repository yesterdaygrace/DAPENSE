@extends('layouts.applayout')
@section('content')

@include('components.admin-sidebar', ['activeMenu' => 'account-header'])

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Tambah Header COA</h5>
            </div>
            <div class="card-body">
                @if(Session::has('success'))
                <div class="alert alert-success" role="alert">
                    {{ Session::get('success') }}
                </div>
                @endif
                <form action="{{ route('operator/account/header/save') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="kode_header" class="form-label">Kode Header</label>
                        <input style="text-transform: uppercase;" type="text" id="kode_header" name="kode_header" maxlength="7" class="form-control @error('kode_header') is-invalid @enderror" placeholder="Masukkan kode header" value="{{ old('kode_header') }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        @error('kode_header')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="nama_header" class="form-label">Nama Header</label>
                        <input style="text-transform: uppercase;" type="text" id="nama_header" name="nama_header" class="form-control @error('nama_header') is-invalid @enderror" placeholder="Masukkan nama header" value="{{ old('nama_header') }}">
                        @error('nama_header')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="level" class="form-label">Level</label>
                        <select id="level" name="level" class="form-select @error('level') is-invalid @enderror" required>
                            <option value="">-- Pilih level --</option>
                            <option value="0" {{ old('level') == '0' ? 'selected' : '' }}>0</option>
                            <option value="1" {{ old('level') == '1' ? 'selected' : '' }}>1</option>
                            <option value="2" {{ old('level') == '2' ? 'selected' : '' }}>2</option>
                            <option value="3" {{ old('level') == '3' ? 'selected' : '' }}>3</option>
                        </select>
                        @error('level')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="parent_id" class="form-label">Parent Header</label>
                        <select id="parent_id" name="parent_id" class="form-control @error('parent_id') is-invalid @enderror">
                            <option value="">NULL</option>
                            {{-- semua header dikirim ke JS --}}
                            @foreach($headerCoas as $headerCoa)
                            <option value="{{ $headerCoa->id }}" data-level="{{ $headerCoa->level }}">
                                {{ $headerCoa->kode_header }} - {{ $headerCoa->nama_header }}
                            </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- / Content -->
    <div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const levelSelect = document.getElementById('level');
        const parentSelect = document.getElementById('parent_id');
        const allOptions = Array.from(parentSelect.options);

        function filterParentOptions() {
            const selectedLevel = levelSelect.value;
            parentSelect.innerHTML = ''; // kosongkan dulu

            if (selectedLevel === '0') {
                // hanya NULL
                parentSelect.innerHTML = '<option value="">NULL</option>';
            } else {
                // tambahkan NULL dulu
                parentSelect.innerHTML = '<option value="">NULL</option>';
                // ambil parent dengan level = selectedLevel - 1
                const parentLevel = parseInt(selectedLevel) - 1;
                allOptions.forEach(opt => {
                    if (opt.dataset.level == parentLevel) {
                        parentSelect.appendChild(opt.cloneNode(true));
                    }
                });
            }
        }

        // jalankan saat level berubah
        levelSelect.addEventListener('change', filterParentOptions);

        // jalankan sekali saat halaman load (untuk old value)
        filterParentOptions();
    });
</script>
@endsection