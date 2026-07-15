@extends('layouts.applayout')
@section('title', 'Neraca Saldo')
@section('content')

@php
use Illuminate\Support\Carbon;
$monthParam = request()->query('month');
$bulanTahun = $monthParam ? Carbon::parse($monthParam)->translatedFormat('F Y') : '-';
@endphp

<x-dashboard.page-header
    title="Neraca Saldo Bulan {{ $bulanTahun }}"
    description="Ringkasan saldo seluruh akun"
    :actions="'<a href=\'' . route('rootsuperuser/neracasaldo/exportexcel', ['periode_id' => $periode->id]) . '?month=' . request()->query('month') . '\' class=\'btn-success btn-sm\'><i data-lucide=\'file-spreadsheet\' class=\'w-4 h-4\'></i> Export Excel</a> <a href=\'' . route('rootsuperuser/neracasaldo/exportpdf', ['periode_id' => $periode->id]) . '?month=' . request()->query('month') . '\' class=\'btn-secondary btn-sm\'><i data-lucide=\'file-text\' class=\'w-4 h-4\'></i> Export PDF</a>'"
/>

<div class="card rounded-card border border-gray-100 shadow-card">
  <div class="card-body p-6 overflow-x-auto">
    @if(!empty($headerCoas) && $headerCoas->count())
    <table class="data-table w-full">
      <thead>
        <tr>
          <th class="sticky top-0 bg-gray-50 z-10">Kode Perk</th>
          <th class="sticky top-0 bg-gray-50 z-10">Nama Perkiraan</th>
          <th class="sticky top-0 bg-gray-50 z-10 text-right num-col">Saldo Awal</th>
          <th class="sticky top-0 bg-gray-50 z-10 text-right num-col">Debit</th>
          <th class="sticky top-0 bg-gray-50 z-10 text-right num-col">Kredit</th>
          <th class="sticky top-0 bg-gray-50 z-10 text-right num-col">Saldo Akhir</th>
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
        echo "<tr class=\"hover:bg-gray-50/50 transition-colors\">";
        echo "<td class=\"font-semibold\">{$indent}{$header->kode_header}</td>";
        echo "<td class=\"font-semibold\">{$header->nama_header}</td>";
        echo "<td class=\"text-right num-col font-semibold\">" . formatSaldo($saldoAwal) . "</td>";
        echo "<td class=\"text-right num-col font-semibold\">" . formatSaldo($header->total_debit) . "</td>";
        echo "<td class=\"text-right num-col font-semibold\">" . formatSaldo($header->total_kredit) . "</td>";
        echo "<td class=\"text-right num-col font-semibold\">" . formatSaldo($saldoAkhir) . "</td>";
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

        echo "<tr class=\"hover:bg-gray-50/50 transition-colors\">";
        echo "<td>{$indent}&nbsp;&nbsp;&nbsp;{$coa->kode_akun}</td>";
        echo "<td>&nbsp;&nbsp;{$coa->nama_akun}</td>";
        echo "<td class=\"text-right num-col\">" . formatSaldo($coa->saldo_awal_debit - $coa->saldo_awal_kredit) . "</td>";
        echo "<td class=\"text-right num-col\">" . formatSaldo($coa->total_debit) . "</td>";
        echo "<td class=\"text-right num-col\">" . formatSaldo($coa->total_kredit) . "</td>";
        echo "<td class=\"text-right num-col\">" . formatSaldo($coa->saldo_akhir) . "</td>";
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
    @else
    <x-dashboard.empty-state
        icon="file-x"
        title="Belum ada data Neraca Saldo"
        description="Tidak ada data untuk periode yang dipilih."
    />
    @endif
  </div>
</div>

@endsection
