@extends('layouts.applayout')
@section('content')
@include('components.admin-sidebar', ['activeMenu' => 'neracasaldo'])

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                @php
                use Illuminate\Support\Carbon;

                $monthParam = request()->query('month'); // Contoh: '2025-07'
                $bulanTahun = $monthParam ? Carbon::parse($monthParam)->translatedFormat('F Y') : '-';
                @endphp

                <h2>Neraca Saldo Bulan {{ $bulanTahun }}</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin/neracasaldo/exportexcel', ['periode_id' => $periode->id]) }}?month={{ request()->query('month') }}"
                        class="btn btn-success">
                        Export Excel
                    </a>
                    <a href="{{ route('admin/neracasaldo/exportpdf', ['periode_id' => $periode->id]) }}?month={{ request()->query('month') }}"
                        class="btn btn-warning">
                        Export PDF
                    </a>
                </div>
            </div>
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
                            $excludedHeaders=[
                            ['kode'=> '121100', 'nama' => 'Aktiva Lancar'],
                            ['kode' => '131100'],
                            ['kode' => '20000'],
                            ['kode' => '200000'],
                            ['kode' => '250000'],
                            ['kode' => '70110'],
                            ['kode' => '701100'],
                            ['kode' => '704100'],
                            ['kode' => '81210'],
                            ['kode' => '812100'],
                            ['kode' => '812400'],
                            ];

                            $isExcluded = false;
                            foreach ($excludedHeaders as $excluded) {
                            if (
                            $header->kode_header == $excluded['kode'] &&
                            (!isset($excluded['nama']) || $header->nama_header == $excluded['nama'])
                            ) {
                            $isExcluded = true;
                            break;
                            }
                            }

                            if (!$isExcluded) {
                            $overrideToZero = in_array($header->kode_header, ['100', '1001']);
                            $saldoAwal = $overrideToZero ? 0 : ($header->total_saldo_awal_debit - $header->total_saldo_awal_kredit);
                            $saldoAkhir = $overrideToZero ? 0 : $header->total_saldo_akhir;

                            if (
                            $saldoAwal == 0 &&
                            $header->total_debit == 0 &&
                            $header->total_kredit == 0 &&
                            $saldoAkhir == 0
                            ) {
                            return;
                            }

                            $indent = str_repeat('&nbsp;', $level * 4);
                            echo "<tr>";
                                echo "<td><strong>{$indent}{$header->kode_header}</strong></td>";
                                echo "<td><strong>{$header->nama_header}</strong></td>";
                                echo "<td><strong>" . formatSaldo($saldoAwal) . "</strong></td>";
                                echo "<td><strong>" . formatSaldo($header->total_debit) . "</strong></td>";
                                echo "<td><strong>" . formatSaldo($header->total_kredit) . "</strong></td>";
                                echo "<td><strong>" . formatSaldo($saldoAkhir) . "</strong></td>";
                                echo "</tr>";

                            $skipCoa = $header->nama_header === 'PEMBELIAN-PENJUALAN AKT.OPRNL';

                            if (
                            (strlen($header->kode_header) == 3) &&
                            !$skipCoa
                            ) {
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
                                echo "<td>&nbsp;&nbsp;{$coa->nama_akun}</td>";
                                echo "<td>" . formatSaldo($coa->saldo_awal_debit - $coa->saldo_awal_kredit) . "</td>";
                                echo "<td>" . formatSaldo($coa->total_debit) . "</td>";
                                echo "<td>" . formatSaldo($coa->total_kredit) . "</td>";
                                echo "<td>" . formatSaldo($coa->saldo_akhir) . "</td>";
                                echo "</tr>";
                            }
                            }
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