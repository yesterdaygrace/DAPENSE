@extends('layouts.operatorlayout')
@section('content')

<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('operator/dashboard') }}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder ms-2">{{ Auth::user()->name }}</span>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item active">
            <a href="{{ route('operator/dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('operator/periodes') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calendar"></i>
                <div data-i18n="Analytics">Periode</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                <div data-i18n="Layouts">Accounts</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/account/header') }}" class="menu-link">
                        <div data-i18n="Without menu">Header</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/account/coa') }}" class="menu-link">
                        <div data-i18n="Without menu">COA</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/account/headercoa') }}" class="menu-link">
                        <div data-i18n="Without menu">Combine Header & COA</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="{{ route('operator/saldoawal') }}" class="menu-link">
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
                    <a href="{{ route('operator/jurnaling') }}" class="menu-link">
                        <div data-i18n="Without menu">Kas Masuk</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling/kaskeluar') }}" class="menu-link">
                        <div data-i18n="Without menu">Kas Keluar</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling/bankmasuk') }}" class="menu-link">
                        <div data-i18n="Without menu">Bank Masuk</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling/bankkeluar') }}" class="menu-link">
                        <div data-i18n="Without menu">Bank Keluar</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling/memorial') }}" class="menu-link">
                        <div data-i18n="Without menu">Memorial</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling/memorialpenutup') }}" class="menu-link">
                        <div data-i18n="Without menu">Memorial (Penutup)</div>
                    </a>
                </li>
            </ul>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="{{ route('operator/jurnaling/showing') }}" class="menu-link">
                        <div data-i18n="Without menu">Tampil</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="{{ route('operator/bukubesar') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-book"></i>
                <div data-i18n="Analytics">Buku Besar</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="{{ route('operator/neracasaldo/') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calculator"></i>
                <div data-i18n="Analytics">Neraca Saldo</div>
            </a>
        </li>
    </ul>
</aside>
<!-- / Menu -->

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        @php
        \Carbon\Carbon::setLocale('id');
        @endphp

        <div class="row">
            <div class="mb-3 col-lg-12 order-0">
                <div class="card shadow-lg border-0 bg-light p-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h3 class="fw-bold text-primary">{{ Auth::user()->usertype }}</h3>
                            <h5 class="text-muted">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</h5>
                        </div>
                        <h5 class="text-secondary">Selamat Datang, <span class="fw-semibold">{{ Auth::user()->name }}</span></h5>
                    </div>
                </div>
            </div>
        </div>



        <div class="row">
            @php
            $menus = [
            ['name' => 'Periode', 'route' => 'operator/periodes', 'icon' => 'bx bx-calendar'],
            ['name' => 'COA (Charts of Account)', 'route' => 'operator/account/coa', 'icon' => 'bx bx-spreadsheet'],
            ['name' => 'Saldo Awal', 'route' => 'operator/saldoawal', 'icon' => 'bx bx-money'],
            ['name' => 'Jurnaling', 'route' => 'operator/jurnaling', 'icon' => 'bx bx-notepad'],
            ['name' => 'Buku Besar', 'route' => 'operator/bukubesar', 'icon' => 'bx bx-book'],
            ['name' => 'Neraca Saldo', 'route' => 'operator/neracasaldo/', 'icon' => 'bx bx-calculator'],
            ];
            @endphp

            @foreach ($menus as $menu)
            <div class="col-md-4 col-lg-3 mb-3">
                <a href="{{ route($menu['route']) }}" class="text-decoration-none">
                    <div class="card text-center shadow-lg p-3">
                        <i class="menu-icon tf-icons {{ $menu['icon'] }} fs-1 mb-2"></i>
                        <h6 class="fw-bold">{{ $menu['name'] }}</h6>
                    </div>
                </a>
            </div>
            @endforeach
        </div>

    </div>
    <!-- / Content -->

    <div class="content-backdrop fade"></div>
</div>
@endsection


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.toggle-rekap');

        buttons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const form = this.closest('form');
                fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.textContent = this.textContent === 'Unrekap' ? 'Rekap Jurnal' : 'Unrekap';
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    });
</script>