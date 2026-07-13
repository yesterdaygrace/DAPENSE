@extends('layouts.applayout')
@section('content')
@include('components.admin-sidebar', ['activeMenu' => 'neracasaldo'])

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <a href="{{ route('admin/neracasaldo/export', ['periode_id' => $periode->id]) }}?month={{ request()->query('month') }}" class="btn btn-success">
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