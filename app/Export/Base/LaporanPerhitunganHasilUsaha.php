<?php

namespace App\Export\Base;

use App\Models\Jurnaling;
use App\Models\Otorisator;
use App\Models\Periode;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class LaporanPerhitunganHasilUsaha implements FromCollection, WithColumnWidths, WithEvents, WithHeadings, WithTitle
{
    protected $periode_id;

    protected $month;

    public function __construct($periode_id, $month)
    {
        $this->periode_id = $periode_id;
        $this->month = $month;
        Carbon::setLocale('id');
    }

    public function collection()
    {
        $cleanMonth = preg_replace('/[^0-9\-]/', '', $this->month);
        $selectedMonth = Carbon::parse($cleanMonth . '-01');
        $previousMonth = $selectedMonth->copy()->subMonth();

        $sections = [
            '^PENDAPATAN INVESTASI' => [
                'Bunga/Basi Hasil' => 'custom_bunga',
                'Dividen' => [51200001, 51209999],
                'Sewa' => [100, 100],
                'Laba(Rugi)Pelepasan Investasi' => [51510000, 51519999],
                'Pendapatan Investasi Lain' => [100, 100],
            ],
            '^BEBAN INVESTASI' => [
                'Beban Transaksi' => [61010001, 61119999],
                'Beban Pemeliharaaan Tanah & Bangunan (PBB)' => [61210001, 61219999],
                'Beban Penyusutan Bangunan' => [100, 100],
                'Beban Manajer Investasi' => [100, 100],
                'Beban Custody' => [61610002],
                'Beban Investasi Lain' => [61610001],
            ],
            '^BEBAN OPERASIONAL' => [
                'Gaji/honor Kary,Pengurus & Dewan Pengawas' => [70111001, 70119999],
                'Beban Kantor' => [70210001, 70219999],
                'Beban Pemeliharaan (PBB)' => [100, 100],
                'Beban Penyusutan' => [61310001, 61319999],
                'Beban Jasa Pihak Ketiga' => [70310001, 70319999],
                'Beban Operasional Lain' => [70410000, 70419999],
            ],
            '^PENDAPATAN DAN BEBAN LAIN-LAIN' => [
                'Pendapatan Lain di Luar Investasi' => [81211001, 81249999],
                'Beban Lain di Luar Investasi & Operasional' => [82220001, 82319999],
            ],
        ];

        $result = [];
        $totals = [];

        foreach ($sections as $section => $accounts) {
            $sectionName = strtoupper(str_replace('^', '', $section));
            $result[] = [$sectionName, '', ''];

            $totalCurrent = $totalLast = 0;

            foreach ($accounts as $name => $range) {
                $mode = in_array($name, [
                    'Pendapatan Lain di Luar Investasi',
                    'Bunga/Basi Hasil',
                    'Dividen',
                    'Sewa',
                    'Laba(Rugi)Pelepasan Investasi',
                    'Pendapatan Investasi Lain',
                ]) ? 'kredit' : 'debit';

                $isNegative = str_contains(strtolower($name), 'beban');

                if ($range === 'custom_bunga') {
                    $combinedRanges = [
                        [51110001, 51129999],
                        [51310001, 51319999],
                        [51610001, 51819999],
                    ];
                    $last = $this->sumMultipleRanges($combinedRanges, $previousMonth, $mode);
                    $current = $this->sumMultipleRanges($combinedRanges, $selectedMonth, $mode);
                } else {
                    $last = $this->getSaldoAkhir($range, $previousMonth, $mode);
                    $current = $this->getSaldoAkhir($range, $selectedMonth, $mode);
                }

                if ($isNegative) {
                    $last = abs($last);
                    $current = abs($current);
                }

                $displayCurrent = $this->formatSaldo($current);
                $displayLast = $this->formatSaldo($last);

                $result[] = [$name, $displayCurrent, $displayLast];

                if ($sectionName === 'PENDAPATAN DAN BEBAN LAIN-LAIN') {
                    $multiplier = $isNegative ? -1 : 1;
                    $totalCurrent += $multiplier * $current;
                    $totalLast += $multiplier * $last;
                } else {
                    $totalCurrent += $current;
                    $totalLast += $last;
                }
            }

            $result[] = [
                'Total ' . ucwords(strtolower($sectionName)),
                $this->formatSaldo($totalLast),
                $this->formatSaldo($totalCurrent),
            ];

            $totals[$sectionName] = [$totalCurrent, $totalLast];

            if ($sectionName === 'BEBAN INVESTASI') {
                $hasilInvestasiCurrent = $totals['PENDAPATAN INVESTASI'][0] - $totals['BEBAN INVESTASI'][0];
                $hasilInvestasiLast = $totals['PENDAPATAN INVESTASI'][1] - $totals['BEBAN INVESTASI'][1];
                $result[] = [
                    'HASIL USAHA INVESTASI',
                    $this->formatSaldo($hasilInvestasiLast),
                    $this->formatSaldo($hasilInvestasiCurrent),

                ];
            }

            if ($sectionName === 'PENDAPATAN DAN BEBAN LAIN-LAIN') {
                $hasilUsahaSetelahPajakCurrent = $hasilInvestasiCurrent - $totals['BEBAN OPERASIONAL'][0];
                $hasilUsahaSetelahPajakLast = $hasilInvestasiLast - $totals['BEBAN OPERASIONAL'][1];
                $result[] = [
                    'HASIL USAHA SEBELUM PAJAK',
                    $this->formatSaldo($hasilUsahaSetelahPajakLast),
                    $this->formatSaldo($hasilUsahaSetelahPajakCurrent),
                ];

                $hasilUsahaSebelumPajakCurrent = $hasilUsahaSetelahPajakCurrent + $totals['PENDAPATAN DAN BEBAN LAIN-LAIN'][0];
                $hasilUsahaSebelumPajakLast = $hasilUsahaSetelahPajakLast + $totals['PENDAPATAN DAN BEBAN LAIN-LAIN'][1];
                $result[] = [
                    'HASIL USAHA SETELAH PAJAK',
                    $this->formatSaldo($hasilUsahaSebelumPajakLast),
                    $this->formatSaldo($hasilUsahaSebelumPajakCurrent),
                ];
            }
        }

        return collect($result);
    }

    private function sumMultipleRanges(array $ranges, Carbon $date, string $mode)
    {
        $total = 0;
        foreach ($ranges as $range) {
            $total += $this->getSaldoAkhir($range, $date, $mode);
        }

        return $total;
    }

    private function getSaldoAkhir(array $range, Carbon $date, string $mode = 'debit')
    {
        $periodeId = $this->resolvePeriodeId($date);

        $jurnal = Jurnaling::where('periode_id', $periodeId)
            ->whereMonth('tanggal_jurnal', $date->month)
            ->whereYear('tanggal_jurnal', $date->year)
            ->whereHas('coa', function ($q) use ($range) {
                count($range) === 2
                    ? $q->whereBetween('kode_akun', $range)
                    : $q->where('kode_akun', $range[0]);
            })
            ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(kredit), 0) as total_kredit')
            ->first();

        return $mode === 'debit' ? $jurnal->total_debit : $jurnal->total_kredit;
    }

    private function resolvePeriodeId(Carbon $date)
    {
        return Periode::whereDate('tanggal_awal', '<=', $date->startOfMonth())
            ->whereDate('tanggal_akhir', '>=', $date->endOfMonth())
            ->value('id');
    }

    private function formatSaldo($value)
    {
        if ($value === null || $value == 0) {
            return '-';
        }

        if ($value < 0) {
            return '(' . number_format(abs($value), 2, ',', '.') . ')';
        }

        return number_format($value, 2, ',', '.');
    }

    public function headings(): array
    {
        $selectedMonth = Carbon::parse($this->month . '-01');
        $previousMonth = $selectedMonth->copy()->subMonth();

        return [
            'ASET',
            $previousMonth->translatedFormat('F Y'),
            $selectedMonth->translatedFormat('F Y'),
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 50, 'B' => 30, 'C' => 30];
    }

    public function title(): string
    {
        return 'Laporan Perhitungan Hasil Usaha';
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

                $sheet->mergeCells('A1:C1');
                $sheet->setCellValue('A1', '4');
                $sheet->getStyle('A1')->applyFromArray([
                    'alignment' => ['horizontal' => 'center'],
                    'font' => ['size' => 20],
                ]);

                $titles = [
                    'A2' => 'DANA PENSIUN SEKOLAH KRISTEN',
                    'A3' => 'SINODE GKJ & GKI JAWA TENGAH SALATIGA',
                    'A4' => '(PROGRAM PENSIUM MANFAAT PASTI)',
                    'A5' => 'LAPORAN PERHITUNGAN HASIL USAHA',
                    'A6' => 'Per ' . $previousMonth->translatedFormat('F Y') . ' & ' . $selectedMonth->translatedFormat('F Y'),
                ];

                foreach ($titles as $cell => $text) {
                    $sheet->mergeCells($cell . ':C' . substr($cell, 1));
                    $sheet->setCellValue($cell, $text);
                    $sheet->getStyle($cell)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 12],
                        'alignment' => ['horizontal' => 'center'],
                    ]);
                }

                $sheet->setCellValue('A7', '');
                $sheet->setCellValue('A8', '');

                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle("A8:A$highestRow")->getAlignment()->setHorizontal('left');
                $sheet->getStyle("B8:C$highestRow")->getAlignment()->setHorizontal('right');

                for ($row = 8; $row <= $highestRow; $row++) {
                    $val = trim((string) $sheet->getCell("A$row")->getValue());
                    if (stripos($val, 'Total') !== false) {
                        $sheet->getStyle("A$row:C$row")->getFont()->setBold(true);

                        $sheet->getStyle('B' . ($row - 1) . ':C' . ($row - 1))->applyFromArray([
                            'borders' => [
                                'bottom' => [
                                    'borderStyle' => Border::BORDER_THICK,
                                    'color' => ['rgb' => '000000'],
                                ],
                            ],
                        ]);
                    }

                    if (trim(strtoupper($val)) === 'HASIL USAHA SETELAH PAJAK') {
                        $sheet->getStyle('B' . ($row - 1) . ':C' . ($row - 1))->applyFromArray([
                            'borders' => [
                                'bottom' => [
                                    'borderStyle' => Border::BORDER_DOUBLE,
                                    'color' => ['rgb' => '000000'],
                                ],
                            ],
                        ]);

                        $sheet->getStyle("A$row:C$row")->getFont()->setBold(true);

                        $sheet->getStyle('B' . ($row + 1) . ':C' . ($row + 1))->applyFromArray([
                            'borders' => [
                                'top' => [
                                    'borderStyle' => Border::BORDER_DOUBLE,
                                    'color' => ['rgb' => '000000'],
                                ],
                            ],
                        ]);
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

                $sheet->getStyle('A' . ($highestRow + 3) . ":C$footerRow")->applyFromArray([
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
            },
        ];
    }
}
