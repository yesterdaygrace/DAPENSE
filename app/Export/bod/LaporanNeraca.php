<?php

namespace App\Export\bod;

use Illuminate\Support\Facades\Log;
use App\Models\Otorisator;
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
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Carbon\Carbon;

class LaporanNeraca implements WithTitle, FromCollection, WithHeadings, WithEvents, WithColumnWidths
{
    protected $periode_id, $month;

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
            '^INVESTASI NILAI BUKU' => [
                'Surat Berharga Negara' => [10710000, 10719999],
                'Tabungan' => [10610001, 10619999],
                'Deposito On Call' => [10020000, 10029999],
                'Deposito Berjangka' => [10010000, 10019999],
                'Sertifikat Deposito' => [100, 100],
                'Sertifikat Bank Indonesia' => [100, 100],
                'Saham' => [10100000, 10109999],
                'Obligasi' => [10210000, 10219999],
                'Sukuk' => [10810000, 10819999],
                'Unit Penyertaan Reksadana' => [10110001, 10119999],
                'Efek beragun aset dari kontrak investasi kolektif' => [10910001, 10919999],
                'Kontrak opsi saham penempatan langsung' => [10310001, 10319999],
                'Tanah' => [10410001, 10419999],
                'Bangunan' => [10510001, 10519999],
                'Tanah & Bangunan' => [10410001, 10519999],
            ],
            '^SELISIH PENILAIAN INVESTASI' => [
                'Selisih Penilaian Saham' => [11200001, 11209999],
                'Selisih Penilaian Tanah' => [100, 100],
                'Selisih Penilaian Bangunan' => [100, 100],
            ],
            '^ASET LANCAR DILUAR INVESTASI' => [
                'Kas & Bank' => [12110000, 12129999],
                'Iuran Normal Pemberi Kerja' => [12220002],
                'Iuran Normal Peserta' => [12220001],
                'Iuran Tambahan' => [12220003],
                'Piutang bunga keterlambatan iuran' => [12230001, 12239999],
                'Beban dibayar muka' => [12310000, 12329999],
                'Piutang investasi' => [12240001, 12249999],
                'Piutang hasil investasi' => [12210001, 12219999],
                'Piutang lain lain' => [12250001],
            ],
            '^ASET OPERASIONAL' => [
                'Tanah & Bangunan' => [13110001, 13129999],
                'Kendaraan' => [13140001, 13149999],
                'Peralatan Komputer' => [13150001, 13159999],
                'Peralatan Kantor' => [13130001, 13139999],
                'Aktiva Operasional Lain' => [13160001, 13169999],
                'Akumulasi Penyusutan' => [13210001, 13259999],
            ],
            '^LIABILITAS' => [
                'NILAI KINI AKTUARIAL' => [24010001, 24619999],
                'SELISIH NILAI KINI AKTUARIAL' => [21110001, 25009999],
                '^LIABILITAS DI LUAR NILAI KINI AKTUARIAL' => [
                    'Utang Manfaat Pensiun Jatuh Tempo' => [23810001, 23819999],
                    'Utang Investasi' => [23210001, 23219999],
                    'Pendapatan Diterima Dimuka' => [23410001, 23419999],
                    'Beban yang Masih Harus Dibayar ' => [23510001, 23519999],
                    'Liabilitas Lain' => [23610001, 23619999],
                ],
            ],
        ];

        $result = [];
        $asetNeto = ['last' => 0, 'current' => 0];
        $totalAsetLain = ['last' => 0, 'current' => 0];
        $totalSemua = ['last' => 0, 'current' => 0];

        foreach ($sections as $section => $items) {
            $result[] = [$section, '', ''];
            $currentTotal = $lastTotal = 0;

            foreach ($items as $label => $config) {
                if (is_array($config) && str_starts_with($label, '^')) {
                    $result[] = [$label, '', ''];
                    foreach ($config as $subLabel => $subConfig) {
                        $range = $subConfig['range'] ?? $subConfig;
                        $add = $subConfig['add'] ?? [];
                        $offset = $subConfig['offset'] ?? [];

                        $last = $this->getSaldoAkhir($range, $previousMonth, $subLabel, 'Last', 0, $offset, $add);
                        $current = $this->getSaldoAkhir($range, $selectedMonth, $subLabel, 'Current', $last, $offset, $add);

                        $result[] = [
                            $subLabel,
                            $this->formatSaldo($current),
                            $this->formatSaldo($last),
                        ];

                        $currentTotal += $current;
                        $lastTotal += $last;
                    }
                } else {
                    $range = $config['range'] ?? $config;
                    $add = $config['add'] ?? [];
                    $offset = $config['offset'] ?? [];

                    $last = $this->getSaldoAkhir($range, $previousMonth, $label, 'Last', 0, $offset, $add);
                    $current = $this->getSaldoAkhir($range, $selectedMonth, $label, 'Current', $last, $offset, $add);

                    $result[] = [
                        $label,
                        $this->formatSaldo($last),
                        $this->formatSaldo($current),
                    ];

                    $currentTotal += $current;
                    $lastTotal += $last;
                }
            }

            $result[] = ['Total ' . str_replace('^', '', $section), $this->formatSaldo($currentTotal), $this->formatSaldo($lastTotal)];
            if (str_contains($section, 'LIABILITAS')) {
                $asetNeto['last'] -= $lastTotal;
                $asetNeto['current'] -= $currentTotal;
            } else {
                $asetNeto['last'] += $lastTotal;
                $asetNeto['current'] += $currentTotal;

                $totalAsetLain['last'] += $lastTotal;
                $totalAsetLain['current'] += $currentTotal;

                $totalSemua['last'] += $lastTotal;
                $totalSemua['current'] += $currentTotal;
            }


            if (str_contains($section, 'ASET OPERASIONAL')) {
                $result[] = ['ASET LAIN LAIN', $this->formatSaldo($totalAsetLain['last']), $this->formatSaldo($totalAsetLain['current'])];
            }
        }

        // ✅ Final TOTAL SEMUA
        $result[] = ['TOTAL SEMUA',  $this->formatSaldo($totalSemua['last']), $this->formatSaldo($totalSemua['current'])];

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

        if (!empty($addAccounts)) {
            $final += $this->getSaldoAkhir($addAccounts, $date, $label . ' (Add)', $pos, 0);
        }

        if (!empty($offsetAccounts)) {
            $final -= $this->getSaldoAkhir($offsetAccounts, $date, $label . ' (Offset)', $pos, 0);
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
        return 'Laporan Neraca';
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
                $sheet->setCellValue('A1', '3');
                $sheet->getStyle('A1')->applyFromArray([
                    'alignment' => ['horizontal' => 'center'],
                    'font' => ['size' => 20],
                ]);

                $titles = [
                    'A2' => 'DANA PENSIUN SEKOLAH KRISTEN',
                    'A3' => 'SINODE GKJ & GKI JAWA TENGAH SALATIGA',
                    'A4' => '(PROGRAM PENSIUM MANFAAT PASTI)',
                    'A5' => 'LAPORAN NERACA',
                    'A6' => 'Per ' . $previousMonth->translatedFormat('F Y') . ' & ' . $selectedMonth->translatedFormat('F Y')
                ];

                $sheet->setCellValue('A7', '');
                $sheet->setCellValue('A8', '');

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
                        // Bold text for Total row
                        $sheet->getStyle("A$row:C$row")->getFont()->setBold(true);

                        // Add thick TOP border before Total row
                        $sheet->getStyle("B" . ($row - 1) . ":C" . ($row - 1))->applyFromArray([
                            'borders' => [
                                'bottom' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
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
