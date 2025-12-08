<?php

namespace App\Export\admin;

use Illuminate\Support\Facades\Log;
use App\Models\Otorisator;
use App\Models\Jurnaling;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithTitle,
    WithEvents,
    WithColumnWidths
};
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class LaporanPerubahanAsetNeto implements WithTitle, FromCollection, WithHeadings, WithEvents, WithColumnWidths
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
            '^Penambahan' => [
                'Bunga/Basi Hasil' => 'custom_bunga',
                'Dividen' => [51200001, 51209999],
                'Sewa' => [100, 100],
                'Laba(Rugi)Pelepasan Investasi' => [51510000, 51519999],
                'Pendapatan Investasi Lain' => [100, 100],
                'Total Pendapatan Investasi' => [],
                'Peningkatan (Penurunan) Nilai Investasi' => [11200001, 11209999],
                'Iuran Normal Pemberi Kerja' => [22111101, 22111199],
                'Iuran Normal Peserta' => [22111001, 22111099],
                'Iuran Tambahan' => [22112001, 22112999],
                'Pendapatan Lain di Luar Investasi' => [81210001, 81259999],
                'Pengalihan Dana dari DaPen Lain' => [100, 100],
            ],
            '^Pengurangan' => [
                'Beban Investasi' => 'custom_beban_investasi',
                'Beban Operasional' => 'custom_beban_operasional',
                'Beban Lain di Luar Investasi & Operasional' => [82210001, 82229999],
                'Manfaat Pensiun' => [22122001, 22122099],
                'Pajak Penghasilan' => [100, 100],
                'Pengalihan Dana ke DaPen Lain' => [22122101, 22122199],
                'Koreksi Selisih Aktuaria' => [100, 100],
            ],
        ];

        $result = [];
        $grandPenambahanCurrent = 0;
        $grandPenambahanLast = 0;
        $grandPenguranganCurrent = 0;
        $grandPenguranganLast = 0;

        $penambahanYangDihitung = [
            'Total Pendapatan Investasi',
            'Peningkatan (Penurunan) Nilai Investasi',
            'Iuran Normal Pemberi Kerja',
            'Iuran Normal Peserta',
            'Iuran Tambahan',
            'Pendapatan Lain di Luar Investasi',
            'Pengalihan Dana dari DaPen Lain'
        ];

        $forceDebitOnlyNames = [
            'Manfaat Pensiun',
            'Pengalihan Dana ke DaPen Lain',
        ];

        foreach ($sections as $section => $accounts) {
            $isTitle = str_starts_with($section, '^');
            $sectionName = strtoupper(str_replace('^', '', $section));
            $sectionType = strtolower($sectionName) === 'pengurangan' ? 'pengurangan' : 'penambahan';

            if ($isTitle) {
                $result[] = ['Investasi Nilai Buku' => $sectionName, 'Saldo Akhir (Last)' => '', 'Saldo Akhir (Current)' => ''];
            }

            $totalCurrent = $totalLast = 0;
            $subtotalBuffer = [];

            foreach ($accounts as $name => $range) {
                if ($name === 'Total Pendapatan Investasi') {
                    $subtotalCurrent = array_sum(array_column($subtotalBuffer, 'current'));
                    $subtotalLast = array_sum(array_column($subtotalBuffer, 'last'));

                    $result[] = [
                        'Investasi Nilai Buku' => $name,
                        'Saldo Akhir (Last)' => $this->formatSaldo($subtotalLast),
                        'Saldo Akhir (Current)' => $this->formatSaldo($subtotalCurrent),

                    ];

                    $subtotalBuffer = [];
                    if (in_array($name, $penambahanYangDihitung)) {
                        $totalCurrent += abs($subtotalCurrent);
                        $totalLast += abs($subtotalLast);
                    }
                    continue;
                }

                $forceDebitOnly = in_array($name, $forceDebitOnlyNames);

                if ($range === 'custom_beban_operasional') {
                    $combinedRanges = [
                        [70111001, 70412999],
                        [61310001, 61319999],
                    ];
                    $last = $current = 0;
                    foreach ($combinedRanges as $subRange) {
                        $last += $this->getSaldoAkhir($subRange, $previousMonth, $name, $sectionType, $forceDebitOnly);
                        $current += $this->getSaldoAkhir($subRange, $selectedMonth, $name, $sectionType, $forceDebitOnly);
                    }
                } elseif ($range === 'custom_beban_investasi') {
                    $combinedRanges = [
                        [61010001, 61219999],
                        [61610001, 61619999],
                    ];
                    $last = $current = 0;
                    foreach ($combinedRanges as $subRange) {
                        $last += $this->getSaldoAkhir($subRange, $previousMonth, $name, $sectionType, $forceDebitOnly);
                        $current += $this->getSaldoAkhir($subRange, $selectedMonth, $name, $sectionType, $forceDebitOnly);
                    }
                } elseif ($range === 'custom_bunga') {
                    $combinedRanges = [
                        [51110001, 51129999],
                        [51310001, 51319999],
                        [51610001, 51819999],
                    ];
                    $last = $this->sumMultipleRanges($combinedRanges, $previousMonth, $name, $sectionType);
                    $current = $this->sumMultipleRanges($combinedRanges, $selectedMonth, $name, $sectionType);
                } else {
                    $last = $this->getSaldoAkhir($range, $previousMonth, $name, $sectionType, $forceDebitOnly);
                    $current = $this->getSaldoAkhir($range, $selectedMonth, $name, $sectionType, $forceDebitOnly);
                }

                $useBracket = $name === 'Peningkatan (Penurunan) Nilai Investasi';

                $result[] = [
                    'Investasi Nilai Buku' => $name,
                    'Saldo Akhir (Last)' => $this->formatSaldo($last, $useBracket),
                    'Saldo Akhir (Current)' => $this->formatSaldo($current, $useBracket),
                ];

                if ($sectionType === 'penambahan' && in_array($name, $penambahanYangDihitung)) {
                    if ($name === 'Peningkatan (Penurunan) Nilai Investasi') {
                        $totalCurrent += $current;
                        $totalLast += $last;
                    } else {
                        $totalCurrent += abs($current);
                        $totalLast += abs($last);
                    }
                } elseif ($sectionType === 'pengurangan') {
                    $totalCurrent += abs($current);
                    $totalLast += abs($last);
                }

                $subtotalBuffer[] = ['last' => $last, 'current' => $current];
            }

            if (!empty($accounts)) {
                $label = 'Total ' . ucwords(strtolower($sectionName));
                $result[] = [
                    'Investasi Nilai Buku' => '               ' . $label,
                    'Saldo Akhir (Last)' => $this->formatSaldo($totalLast),
                    'Saldo Akhir (Current)' => $this->formatSaldo($totalCurrent),
                ];

                if ($sectionType === 'penambahan') {
                    $grandPenambahanCurrent = $totalCurrent;
                    $grandPenambahanLast = $totalLast;
                } else {
                    $grandPenguranganCurrent = $totalCurrent;
                    $grandPenguranganLast = $totalLast;
                }
            }
        }

        $kenaikanAsetNetoCurrent = $grandPenambahanCurrent - $grandPenguranganCurrent;
        $kenaikanAsetNetoLast = $grandPenambahanLast - $grandPenguranganLast;

        $result[] = [
            'Investasi Nilai Buku' => 'KENAIKKAN (PENURUNAN) ASET NETO',
            'Saldo Akhir (Last)' => $this->formatSaldo($kenaikanAsetNetoLast),
            'Saldo Akhir (Current)' => $this->formatSaldo($kenaikanAsetNetoCurrent),
        ];

        // --- Updated ASET NETO calculation ---
        $asetNetoAwalPeriodeCurrent = \App\Export\admin\LaporanAsetNeto::$asetNeto['last'];
        $asetNetoAkhirLast = $kenaikanAsetNetoLast + $asetNetoAwalPeriodeCurrent;

        $result[] = [
            'Investasi Nilai Buku' => 'ASET NETO AWAL PERIODE',
            'Saldo Akhir (Last)' => $this->formatSaldo($asetNetoAkhirLast),
            'Saldo Akhir (Current)' => $this->formatSaldo(\App\Export\admin\LaporanAsetNeto::$asetNeto['last']),
        ];

        $result[] = [
            'Investasi Nilai Buku' => 'ASET NETO AKHIR PERIODE',
            'Saldo Akhir (Last)' => $this->formatSaldo(\App\Export\admin\LaporanAsetNeto::$asetNeto['last']),
            'Saldo Akhir (Current)' => $this->formatSaldo(\App\Export\admin\LaporanAsetNeto::$asetNeto['current']),
        ];

        return collect($result);
    }

    private function sumMultipleRanges(array $ranges, Carbon $date, string $label, string $sectionType)
    {
        $total = 0;
        foreach ($ranges as $range) {
            $total += $this->getSaldoAkhir($range, $date, $label, $sectionType);
        }
        return $total;
    }

    private function getSaldoAkhir(array $range, Carbon $date, string $label, string $sectionType = 'penambahan', bool $forceDebitOnly = false)
    {
        $periodeId = $this->resolvePeriodeId($date);


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

        $specialIuran = [
            'Iuran Normal Pemberi Kerja',
            'Iuran Normal Peserta',
            'Iuran Tambahan',
        ];

        if (in_array($label, $specialIuran)) {
            return $jurnal->total_kredit - $jurnal->total_debit;
        }

        return $forceDebitOnly
            ? $jurnal->total_debit
            : ($sectionType === 'pengurangan'
                ? $jurnal->total_kredit - $jurnal->total_debit
                : $jurnal->total_debit - $jurnal->total_kredit);
    }

    private function resolvePeriodeId(Carbon $date)
    {
        return \App\Models\Periode::whereDate('tanggal_awal', '<=', $date->startOfMonth())
            ->whereDate('tanggal_akhir', '>=', $date->endOfMonth())
            ->value('id');
    }

    private function formatSaldo($value, $useBracket = false)
    {
        if (!$value) return '-';
        $formatted = number_format(abs($value), 2, ',', '.');
        return ($useBracket && $value < 0) ? "($formatted)" : $formatted;
    }

    public function headings(): array
    {
        $selectedMonth = Carbon::parse($this->month . '-01');
        $previousMonth = $selectedMonth->copy()->subMonth();

        return [
            'ASET',
            'Saldo Akhir (' . $previousMonth->translatedFormat('F Y') . ')',
            'Saldo Akhir (' . $selectedMonth->translatedFormat('F Y') . ')',
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 50, 'B' => 30, 'C' => 30];
    }

    public function title(): string
    {
        return 'Laporan Perubahan Hasil Usaha';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $otorisators = Otorisator::orderBy('id', 'asc')->get();
                $sheet = $event->sheet;
                $selectedMonth = Carbon::parse($this->month . '-01');
                $previousMonth = $selectedMonth->copy()->subMonth();

                $sheet->insertNewRowBefore(1, 7);

                $titles = [
                    'A1' => 'DANA PENSIUN SEKOLAH KRISTEN',
                    'A2' => 'SINODE GKJ & GKI JAWA TENGAH SALATIGA',
                    'A3' => '(PROGRAM PENSIUM MANFAAT PASTI)',
                    'A4' => 'LAPORAN PERUBAHAN HASIL USAHA',
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

                    if (
                        stripos($val, 'Total') !== false ||
                        stripos($val, 'KENAIKKAN ASET NETO') !== false
                    ) {
                        $sheet->getStyle("A$row:C$row")->getFont()->setBold(true);
                    }
                }

                $footerRow = $highestRow + 5;

                $endOfMonthDate = $selectedMonth->copy()->endOfMonth();

                $sheet->mergeCells("A$footerRow:C$footerRow");
                $sheet->setCellValue("A$footerRow", 'Salatiga, ' . $endOfMonthDate->translatedFormat('d F Y'));
                $sheet->getStyle("A$footerRow")->applyFromArray([
                    'alignment' => ['horizontal' => 'center'],
                    'font' => ['size' => 11],
                ]);

                $sheet->getRowDimension($footerRow)->setRowHeight(25);
                $footerRow++;

                $sheet->mergeCells("A$footerRow:C$footerRow");
                $sheet->setCellValue("A$footerRow", 'Pengurus Dana Pensiun Sekolah Kristen');
                $sheet->getStyle("A$footerRow")->applyFromArray([
                    'alignment' => ['horizontal' => 'center'],
                    'font' => ['size' => 11],
                ]);
                $sheet->getRowDimension($footerRow)->setRowHeight(25);
                $footerRow++;

                $sheet->setCellValue("A$footerRow", '');
                $sheet->getRowDimension($footerRow)->setRowHeight(40);
                $footerRow++;

                $left = $otorisators[0] ?? null;
                $right = $otorisators[1] ?? null;

                if ($left) {
                    $sheet->setCellValue("A$footerRow", $left->nama_otorisator);
                    $sheet->getStyle("A$footerRow")->applyFromArray([
                        'font' => ['underline' => true, 'size' => 11],
                        'alignment' => ['horizontal' => 'center'],
                    ]);
                    $sheet->getRowDimension($footerRow)->setRowHeight(30);
                }
                if ($right) {
                    $sheet->setCellValue("C$footerRow", $right->nama_otorisator);
                    $sheet->getStyle("C$footerRow")->applyFromArray([
                        'font' => ['underline' => true, 'size' => 11],
                        'alignment' => ['horizontal' => 'center'],
                    ]);
                    $sheet->getRowDimension($footerRow)->setRowHeight(30);
                }
                $footerRow++;

                if ($left) {
                    $sheet->setCellValue("A$footerRow", $left->jabatan_otorisator);
                }
                if ($right) {
                    $sheet->setCellValue("C$footerRow", $right->jabatan_otorisator);
                }
                $footerRow += 5;

                $sheet->getStyle("A" . ($highestRow + 3) . ":C$footerRow")->applyFromArray([
                    'font' => ['size' => 11],
                    'alignment' => ['horizontal' => 'center'],
                ]);

                $delegate = $event->sheet->getDelegate();
                $delegate->getPageMargins()->setTop(0.75);
                $delegate->getPageMargins()->setBottom(2);
                $delegate->getPageMargins()->setLeft(0.25);
                $delegate->getPageMargins()->setRight(0.25);
                $delegate->getPageMargins()->setHeader(0.3);
                $delegate->getPageMargins()->setFooter(0.3);
                $delegate->getPageSetup()->setFitToWidth(1);
                $delegate->getPageSetup()->setFitToHeight(1);
                $delegate->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
                $delegate->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_FOLIO);

                $protection = $sheet->getProtection();
                $protection->setSheet(true);
                $protection->setPassword('dapense');
            }
        ];
    }
}
