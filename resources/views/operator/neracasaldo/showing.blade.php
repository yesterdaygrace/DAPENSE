@extends('layouts.applayout')
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
        <li class="menu-item">
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
        <li class="menu-item active open">
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
        <li class="menu-item active">
            <a href="{{ route('operator/neracasaldo/') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-calculator"></i>
                <div data-i18n="Analytics">Neraca Saldo</div>
            </a>
        </li>
    </ul>
</aside>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <a href="{{ route('operator/neracasaldo/export', ['periode_id' => $periode->id]) }}?month={{ request()->query('month') }}" class="btn btn-success">
                Export Excel
            </a>
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Kode Perk</th>
                                <th>Nama Perkiraan</th>
                                <th>Saldo Awal</th>
                                <th>Debit</th>
                                <th>Kredit</th>
                                <th>Saldo Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            function formatSaldo($angka) {
                            $formatted = number_format(abs($angka), 2);
                            return $angka < 0 ? "($formatted)" : $formatted;
                                }

                                function renderHeader($header, $level=0) {
                                if (
                                $header->total_saldo_awal_debit == 0 && $header->total_saldo_awal_kredit == 0 &&
                                $header->total_debit == 0 && $header->total_kredit == 0 &&
                                $header->total_saldo_akhir == 0
                                ) {
                                return;
                                }

                                $indent = str_repeat('&nbsp;', $level * 4);
                                echo "<tr>";
                                    echo "<td><strong>{$indent}{$header->kode_header}</strong></td>";
                                    echo "<td><strong>{$header->nama_header}</strong></td>";
                                    echo "<td><strong>" . formatSaldo($header->total_saldo_awal_debit - $header->total_saldo_awal_kredit) . "</strong></td>";
                                    echo "<td><strong>" . formatSaldo($header->total_debit) . "</strong></td>";
                                    echo "<td><strong>" . formatSaldo($header->total_kredit) . "</strong></td>";
                                    echo "<td><strong>" . formatSaldo($header->total_saldo_akhir) . "</strong></td>";
                                    echo "</tr>";

                                foreach ($header->coas as $coa) {
                                if (
                                $coa->saldo_awal_debit == 0 && $coa->saldo_awal_kredit == 0 &&
                                $coa->total_debit == 0 && $coa->total_kredit == 0 &&
                                $coa->saldo_akhir == 0
                                ) {
                                continue;
                                }

                                echo "<tr>";
                                    echo "<td>{$indent}&nbsp;&nbsp;&nbsp;{$coa->kode_akun}</td>";
                                    echo "<td>{$indent}&nbsp;&nbsp;&nbsp;{$coa->kode_akun} - {$coa->nama_akun}</td>";
                                    echo "<td>" . formatSaldo($coa->saldo_awal_debit - $coa->saldo_awal_kredit) . "</td>";
                                    echo "<td>" . formatSaldo($coa->total_debit) . "</td>";
                                    echo "<td>" . formatSaldo($coa->total_kredit) . "</td>";
                                    echo "<td>" . formatSaldo($coa->saldo_akhir) . "</td>";
                                    echo "</tr>";
                                }

                                foreach ($header->children as $child) {
                                renderHeader($child, $level + 1);
                                }
                                }
                                @endphp

                                @foreach($headerCoas as $header)
                                @php renderHeader($header); @endphp
                                @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endsection