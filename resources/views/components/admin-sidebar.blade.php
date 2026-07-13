@props(['activeMenu' => 'dashboard'])

@php
$prefix = Auth::user()->usertype === 'bod' ? 'bod' : (Auth::user()->usertype === 'operator' ? 'operator' : 'admin');
@endphp

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route($prefix . '/dashboard') }}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bolder ms-2">{{ Auth::user()->name }}</span>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="py-1 menu-inner">
        <li class="menu-item {{ $activeMenu === 'dashboard' ? 'active' : '' }}">
            <a href="{{ route($prefix . '/dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Dashboard</div>
            </a>
        </li>

        @if(in_array(Auth::user()->usertype, ['admin', 'operator']))
            @if(in_array(Auth::user()->usertype, ['admin']))
            <li class="menu-item {{ $activeMenu === 'products' ? 'active' : '' }}">
                <a href="{{ route('admin/products') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-user"></i>
                    <div>User Management</div>
                </a>
            </li>
            @endif
            <li class="menu-item {{ str_starts_with($activeMenu, 'periode') ? 'active' : '' }}">
                <a href="{{ route($prefix . '/periodes') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-calendar"></i>
                    <div>Periode</div>
                </a>
            </li>
            <li class="menu-item {{ str_starts_with($activeMenu, 'account') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
                    <div>Accounts</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ $activeMenu === 'account-header' ? 'active' : '' }}">
                        <a href="{{ route($prefix . '/account/header') }}" class="menu-link"><div>Header</div></a>
                    </li>
                    <li class="menu-item {{ $activeMenu === 'account-coa' ? 'active' : '' }}">
                        <a href="{{ route($prefix . '/account/coa') }}" class="menu-link"><div>COA</div></a>
                    </li>
                    <li class="menu-item {{ $activeMenu === 'account-headercoa' ? 'active' : '' }}">
                        <a href="{{ route($prefix . '/account/headercoa') }}" class="menu-link"><div>Combine Header & COA</div></a>
                    </li>
                </ul>
            </li>
            <li class="menu-item {{ $activeMenu === 'saldoawal' ? 'active' : '' }}">
                <a href="{{ route($prefix . '/saldoawal') }}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-money"></i>
                    <div>Saldo Awal</div>
                </a>
            </li>
            <li class="menu-item {{ str_starts_with($activeMenu, 'jurnaling') ? 'active open' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-notepad"></i>
                    <div>Jurnaling</div>
                </a>
                <ul class="menu-sub">
                    <li class="menu-item {{ $activeMenu === 'jurnaling-kasmasuk' ? 'active' : '' }}">
                        <a href="{{ route($prefix . '/jurnaling') }}" class="menu-link"><div>Kas Masuk</div></a>
                    </li>
                    <li class="menu-item {{ $activeMenu === 'jurnaling-kaskeluar' ? 'active' : '' }}">
                        <a href="{{ route($prefix . '/jurnaling/kaskeluar') }}" class="menu-link"><div>Kas Keluar</div></a>
                    </li>
                    <li class="menu-item {{ $activeMenu === 'jurnaling-bankmasuk' ? 'active' : '' }}">
                        <a href="{{ route($prefix . '/jurnaling/bankmasuk') }}" class="menu-link"><div>Bank Masuk</div></a>
                    </li>
                    <li class="menu-item {{ $activeMenu === 'jurnaling-bankkeluar' ? 'active' : '' }}">
                        <a href="{{ route($prefix . '/jurnaling/bankkeluar') }}" class="menu-link"><div>Bank Keluar</div></a>
                    </li>
                    <li class="menu-item {{ $activeMenu === 'jurnaling-memorial' ? 'active' : '' }}">
                        <a href="{{ route($prefix . '/jurnaling/memorial') }}" class="menu-link"><div>Memorial</div></a>
                    </li>
                    <li class="menu-item {{ $activeMenu === 'jurnaling-memorialpenutup' ? 'active' : '' }}">
                        <a href="{{ route($prefix . '/jurnaling/memorialpenutup') }}" class="menu-link"><div>Memorial (Penutup)</div></a>
                    </li>
                    <li class="menu-item {{ $activeMenu === 'jurnaling-showing' ? 'active' : '' }}">
                        <a href="{{ route($prefix . '/jurnaling/showing') }}" class="menu-link"><div>Tampil</div></a>
                    </li>
                </ul>
            </li>
        @endif

        <li class="menu-item {{ $activeMenu === 'bukubesar' ? 'active' : '' }}">
            <a href="{{ route($prefix . '/bukubesar') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-book"></i>
                <div>Buku Besar</div>
            </a>
        </li>
        <li class="menu-item {{ $activeMenu === 'neracasaldo' ? 'active' : '' }}">
            <a href="{{ route($prefix . '/neracasaldo/') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calculator"></i>
                <div>Neraca Saldo</div>
            </a>
        </li>
    </ul>
</aside>
<!-- / Menu -->
