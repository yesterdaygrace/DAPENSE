@extends('layouts.rootsuperuserlayout')
@section('content')
<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('rootsuperuser/dashboard') }}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder ms-2">{{ Auth::user()->name }}</span>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="py-1 menu-inner">
        <!-- Dashboard -->
        <li class="menu-item">
            <a href="{{ route('rootsuperuser/dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('rootsuperuser/products') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Analytics">User Management</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('rootsuperuser/periodes') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calendar"></i>
                <div data-i18n="Analytics">Periode</div>
            </a>
        </li>
        <li class="menu-item active open">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="Layouts">Accounts</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item active">
                    <a href="{{ route('rootsuperuser/account/header') }}" class="menu-link">
                        <div data-i18n="Without menu">Header</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('rootsuperuser/account/coa') }}" class="menu-link">
                        <div data-i18n="Without menu">COA</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('rootsuperuser/account/headercoa') }}" class="menu-link">
                        <div data-i18n="Without menu">Combine Header & COA</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="{{ route('rootsuperuser/saldoawal') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-money"></i>
                <div data-i18n="Analytics">Saldo Awal</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-notepad"></i>
                <div data-i18n="Layouts">Jurnaling</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('rootsuperuser/jurnaling') }}" class="menu-link">
                        <div data-i18n="Without menu">Kas Masuk</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('rootsuperuser/jurnaling/kaskeluar') }}" class="menu-link">
                        <div data-i18n="Without menu">Kas Keluar</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('rootsuperuser/jurnaling/bankmasuk') }}" class="menu-link">
                        <div data-i18n="Without menu">Bank Masuk</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('rootsuperuser/jurnaling/bankkeluar') }}" class="menu-link">
                        <div data-i18n="Without menu">Bank Keluar</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('rootsuperuser/jurnaling/memorial') }}" class="menu-link">
                        <div data-i18n="Without menu">Memorial</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('rootsuperuser/jurnaling/memorialpenutup') }}" class="menu-link">
                        <div data-i18n="Without menu">Memorial (Penutup)</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('rootsuperuser/jurnaling/showing') }}" class="menu-link">
                        <div data-i18n="Without menu">Tampil</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="{{ route('rootsuperuser/bukubesar') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-book"></i>
                <div data-i18n="Analytics">Buku Besar</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="{{ route('rootsuperuser/neracasaldo/') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calculator"></i>
                <div data-i18n="Analytics">Neraca Saldo</div>
            </a>
        </li>
    </ul>
</aside>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="shadow-sm card sm:rounded-lg">
            <div class="text-gray-900 card-body">
                <h1 class="mb-4">Update Header COA</h1>
                <hr />
                @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
                @endif
                <p><a href="{{ route('rootsuperuser/account/header') }}" class="mb-4 btn btn-primary">Kembali</a></p>

                <form action="{{ route('rootsuperuser/account/header/update', $headerCoa->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="kode_header" class="form-label">Kode Header</label>
                        <input style="text-transform: uppercase;" type="text" id="kode_header" name="kode_header" maxlength="7" class="form-control @error('kode_header') is-invalid @enderror" placeholder="Masukkan kode header" value="{{ old('kode_header', $headerCoa->kode_header) }}">
                        @error('kode_header')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="nama_header" class="form-label">Nama Header</label>
                        <input style="text-transform: uppercase;" type="text" id="nama_header" name="nama_header" class="form-control @error('nama_header') is-invalid @enderror" placeholder="Masukkan nama header" value="{{ old('nama_header', $headerCoa->nama_header) }}">
                        @error('nama_header')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="level" class="form-label">Level</label>
                        <select id="level" name="level" class="form-select @error('level') is-invalid @enderror" required>
                            <option value="">-- Pilih level --</option>
                            <option value="0" {{ old('level', $headerCoa->level) == '0' ? 'selected' : '' }}>0</option>
                            <option value="1" {{ old('level', $headerCoa->level) == '1' ? 'selected' : '' }}>1</option>
                            <option value="2" {{ old('level', $headerCoa->level) == '2' ? 'selected' : '' }}>2</option>
                            <option value="3" {{ old('level', $headerCoa->level) == '3' ? 'selected' : '' }}>3</option>
                        </select>
                        @error('level')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="parent_id" class="form-label">Parent Header</label>
                        <select id="parent_id" name="parent_id" class="form-control @error('parent_id') is-invalid @enderror">
                            <option value="">NULL</option>
                            @foreach($headerCoas as $headerCoaItem)
                            <option value="{{ $headerCoaItem->id }}"
                                data-level="{{ $headerCoaItem->level }}"
                                {{ old('parent_id', $headerCoa->parent_id) == $headerCoaItem->id ? 'selected' : '' }}>
                                {{ $headerCoaItem->kode_header }} - {{ $headerCoaItem->nama_header }}
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
                parentSelect.innerHTML = '<option value="">NULL</option>';
            } else {
                parentSelect.innerHTML = '<option value="">NULL</option>';
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

        // jalankan sekali saat halaman load (untuk old value / edit mode)
        filterParentOptions();
    });
</script>

@endsection