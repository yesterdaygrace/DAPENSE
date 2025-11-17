<?php

namespace App\Export\bod;

use Illuminate\Support\Facades\Log;
use App\Models\Jurnaling;
use App\Models\SaldoAwal;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithTitle,
    WithEvents,
    WithColumnWidths
};
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class LaporanInvestasi implements WithTitle, FromCollection, WithHeadings, WithEvents, WithColumnWidths
{
    protected $periode_id, $month;

    public function __construct($periode_id, $month)
    {
        $this->periode_id = $periode_id;
        $this->month = $month;
    }
    public function collection()
    {
        $cleanMonth = preg_replace('/[^0-9\-]/', '', $this->month);
        $selectedMonth = Carbon::parse($cleanMonth . '-01');
        $previousMonth = $selectedMonth->copy()->subMonth();

        $sections = [
            '^INVESTASI NILAI BUKU' => [
                'Surat Berharga Negara' => [10710000, 10719999],
                'Tabungan' => [10610001, 10619999],
                'Deposito On Call' => [10020000, 10029999],
                'Deposito Berjangka' => [10010000, 10019999],
                'Sertifikat Deposito' => [100, 100],
                'Sertifikat Bank Indonesia' => [100, 100],
                'Saham' => [
                    'range' => [10100000, 10109999],
                    'add' => [11200001, 11209999]
                ],
                'Obligasi' => [10210000, 10219999],
                'Sukuk' => [10810000, 10819999],
                'Unit Penyertaan Reksadana' => [10110001, 10119999],
                'Efek beragun aset dari kontrak investasi kolektif' => [10910001, 10919999],
                'Kontrak opsi saham penempatan langsung' => [10310001, 10319999],
                'Tanah' => [10410001, 10419999],
                'Bangunan' => [10510001, 10519999],
                'Tanah & Bangunan' => [10410001, 10519999],
            ]
        ];

        $result = [];

        foreach ($sections as $section => $accounts) {
            $isTitle = str_starts_with($section, '^');
            if ($isTitle) {
                $result[] = [
                    'ASET' => $section,
                    'Saldo Akhir (Current)' => '',
                    '% Current' => '',
                    'Saldo Akhir (Last)' => '',
                    '% Last' => ''
                ];
            }

            $totalCurrent = 0;
            $totalLast = 0;
            $tempRows = [];

            foreach ($accounts as $name => $config) {
                $range = is_array($config) && isset($config['range']) ? $config['range'] : $config;
                $offset = is_array($config) && isset($config['offset']) ? $config['offset'] : [];
                $add = is_array($config) && isset($config['add']) ? $config['add'] : [];

                $last = $this->getSaldoAkhir($range, $previousMonth, $name, 'Last', 0, $offset, $add);
                $current = $this->getSaldoAkhir($range, $selectedMonth, $name, 'Current', $last, $offset, $add);

                $tempRows[] = [
                    'name' => $name,
                    'current' => $current,
                    'last' => $last
                ];

                $totalCurrent += abs($current);
                $totalLast += abs($last);
            }

            // Process each row again with percentages
            foreach ($tempRows as $row) {
                $percentCurrent = ($totalCurrent && $totalCurrent != 0.0) ? (abs($row['current']) / $totalCurrent) * 100 : 0;
                $percentLast = ($totalLast && $totalLast != 0.0) ? (abs($row['last']) / $totalLast) * 100 : 0;

                $result[] = [
                    'ASET' => $row['name'],
                    'Saldo Akhir (Current)' => $this->formatSaldo($row['current']),
                    '% Current' => number_format($percentCurrent, 2, ',', '') . '%',
                    'Saldo Akhir (Last)' => $this->formatSaldo($row['last']),
                    '% Last' => number_format($percentLast, 2, ',', '') . '%',
                ];
            }

            $result[] = [
                'ASET' => '               Total ' . ucwords(strtolower(str_replace('^', '', $section))),
                'Saldo Akhir (Current)' => $this->formatSaldo($totalCurrent),
                '% Current' => '100%',
                'Saldo Akhir (Last)' => $this->formatSaldo($totalLast),
                '% Last' => '100%',
            ];
        }

        return collect($result);
    }




    private function getSaldoAkhir(array $range, Carbon $date, string $label, string $pos, $fallback = 0, array $offsetAccounts = [], array $addAccounts = [])
    {
        $periodeId = $this->resolvePeriodeId($date);

        $saldoAwalQuery = SaldoAwal::where('periode_id', $periodeId)
            ->whereMonth('tanggal_saldo', $date->month)
            ->whereYear('tanggal_saldo', $date->year)
            ->whereHas('coa', function ($q) use ($range) {
                count($range) === 2
                    ? $q->whereBetween('kode_akun', $range)
                    : $q->where('kode_akun', $range[0]);
            });

        $saldoAwal = $saldoAwalQuery
            ->selectRaw('COALESCE(SUM(debit), 0) as debit, COALESCE(SUM(kredit), 0) as kredit')
            ->first();

        $saldoAwalBersih = ($saldoAwal->debit - $saldoAwal->kredit) ?: $fallback;

        $jurnalQuery = Jurnaling::where('periode_id', $periodeId)
            ->whereMonth('tanggal_jurnal', $date->month)
            ->whereYear('tanggal_jurnal', $date->year)
            ->whereHas('coa', function ($q) use ($range) {
                count($range) === 2
                    ? $q->whereBetween('kode_akun', $range)
                    : $q->where('kode_akun', $range[0]);
            });

        $jurnal = $jurnalQuery
            ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(kredit), 0) as total_kredit')
            ->first();

        $final = $saldoAwalBersih + ($jurnal->total_debit - $jurnal->total_kredit);

        // Tambahkan dari akun tambahan jika ada (contoh: Selisih Kurs Saham)
        if (!empty($addAccounts)) {
            $addSaldo = $this->getSaldoAkhir($addAccounts, $date, $label . ' (Add)', $pos, 0);
            $final += $addSaldo;
        }

        // Kurangi offset jika ada (jika digunakan di bagian lain)
        if (!empty($offsetAccounts)) {
            $offsetSaldo = $this->getSaldoAkhir($offsetAccounts, $date, $label . ' (Offset)', $pos, 0);
            $final -= $offsetSaldo;
        }

        return $final;
    }

    private function resolvePeriodeId(Carbon $date)
    {
        return \App\Models\Periode::whereDate('tanggal_awal', '<=', $date->startOfMonth())
            ->whereDate('tanggal_akhir', '>=', $date->endOfMonth())
            ->value('id');
    }

    private function formatSaldo($value)
    {
        if (!$value) return '-';
        $formatted = number_format(abs($value), 2, ',', '.');
        return $value < 0 ? "($formatted)" : $formatted;
    }

    public function headings(): array
    {
        $selectedMonth = Carbon::parse($this->month . '-01');
        $previousMonth = $selectedMonth->copy()->subMonth();

        return [
            'ASET',
            'Saldo Akhir (' . $selectedMonth->translatedFormat('F Y') . ')',
            '% (' . $selectedMonth->translatedFormat('F Y') . ')',
            'Saldo Akhir (' . $previousMonth->translatedFormat('F Y') . ')',
            '% (' . $previousMonth->translatedFormat('F Y') . ')',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 50,
            'B' => 30,
            'C' => 18,
            'D' => 30,
            'E' => 18
        ];
    }

    public function title(): string
    {
        return 'Laporan Investasi';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $selectedMonth = Carbon::parse($this->month . '-01');
                $previousMonth = $selectedMonth->copy()->subMonth();

                $sheet->insertNewRowBefore(1, 7);

                $titles = [
                    'A1' => 'DANA PENSIUN SEKOLAH KRISTEN',
                    'A2' => 'SINODE GKJ & GKI JAWA TENGAH SALATIGA',
                    'A3' => '(PROGRAM PENSIUM MANFAAT PASTI)',
                    'A4' => 'LAPORAN INVESTASI',
                    'A5' => 'Per ' . $previousMonth->translatedFormat('F Y') . ' & ' . $selectedMonth->translatedFormat('F Y')
                ];

                $sheet->setCellValue('A6', '');
                $sheet->setCellValue('A7', '');

                foreach ($titles as $cell => $text) {
                    $sheet->mergeCells($cell . ':C' . substr($cell, 1));
                    $sheet->setCellValue($cell, $text);
                    $sheet->getStyle($cell)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 12],
                        'alignment' => ['horizontal' => 'center'],
                    ]);
                }

                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A8:A$highestRow")->getAlignment()->setHorizontal('left');
                $sheet->getStyle("B8:C$highestRow")->getAlignment()->setHorizontal('right');

                for ($row = 8; $row <= $highestRow; $row++) {
                    $val = trim((string)$sheet->getCell("A$row")->getValue());

                    if (preg_match('/^\^/', $val)) {
                        $text = strtoupper(str_replace('^', '', $val));
                        $sheet->mergeCells("A$row:C$row");
                        $sheet->setCellValue("A$row", $text);
                        $sheet->getStyle("A$row")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 12],
                            'alignment' => ['horizontal' => 'left'],
                        ]);
                    }

                    if (stripos($val, 'Total') !== false) {
                        $sheet->getStyle("A$row:C$row")->getFont()->setBold(true);
                    }
                }
            }
        ];
    }
}
