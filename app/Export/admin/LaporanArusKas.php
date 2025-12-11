<?php

namespace App\Export\admin;

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
use Illuminate\Support\Facades\Log;

class LaporanArusKas implements WithTitle, FromCollection, WithHeadings, WithEvents, WithColumnWidths
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
        $selectedMonth = Carbon::parse(preg_replace('/[^0-9\-]/', '', $this->month) . '-01');
        $previousMonth = $selectedMonth->copy()->subMonth();

        $sections = [
            '^ARUS KAS DARI AKTIVITAS INVESTASI' => [
                'Bunga/Basi Hasil' => 'custom_bunga',
                'Penerimaan Dividen' => 'custom_dividen',
                'Penerimaan Sewa' => 'custom_sewa',
                'Pendapatan Investasi Lain' => 'custom_investasi_lain',
                'Pelepasan Investasi' => 'custom_pelepasan',
                'Penanaman Investasi' => 'custom_penanaman',
                'Pembayaran Beban Investasi' => 'custom_beban_investasi',
            ],
            '^ARUS KAS DARI AKTIVITAS OPERASIONAL' => [
                'Pembayaran Beban Operasional' => 'custom_beban_operasional',
                'Penjualan Aset Operasional' => 'custom_jual_aset_operasional',
                'Pembelian Aset Operasional' => 'custom_beli_aset_operasional',
                'Penjualan Aset Lain-lain' => 'custom_jual_aset_lain',
                'Pembelian Aset Lain-lain' => 'custom_beli_aset_lain',
                'Pendapatan Lain di Luar Investasi' => 'custom_pend_luar_inv',
                'Beban Lain di Luar Investasi & Operasional' => 'custom_beban_luar',
            ],
            '^ARUS KAS DARI AKTIVITAS PENDANAAN' => [
                'Penerimaan Iuran Normal Pemberi Kerja' => 'custom_iuran_pemberi',
                'Penerimaan Iuran Normal Peserta' => 'custom_iuran_peserta',
                'Penerimaan Iuran Tambahan' => 'custom_iuran_tambahan',
                'Penerimaan Bunga Keterlambatan Iuran' => 'custom_bunga_iuran',
                'Penerimaan Pengalihan Dana dari DP lain' => 'custom_terima_dana',
                'Pembayaran Pengalihan Dana ke DP lain' => 'custom_bayar_dana',
                'Pembayaran Manfaat Pensiun' => 'custom_manfaat_pensiun',
            ],
        ];

        $result = [];

        foreach ($sections as $section => $items) {
            $sectionName = strtoupper(str_replace('^ARUS KAS DARI AKTIVITAS ', '', $section));
            $result[] = [strtoupper(str_replace('^', '', $section)), '', ''];

            $totalCurrent = 0;
            $totalLast    = 0;

            foreach ($items as $label => $key) {
                $ranges = $this->getCustomRanges($key);
                $last = $this->sumMultipleRanges($ranges, $previousMonth, $key);
                $current = $this->sumMultipleRanges($ranges, $selectedMonth, $key);

                $totalCurrent += $current;
                $totalLast += $last;

                $result[] = [
                    $label,
                    $this->formatDisplay($last),
                    $this->formatDisplay($current)
                ];
            }

            $result[] = [
                'Total ' . ucwords(strtolower(str_replace('^', '', $section))),
                $this->formatDisplay($totalLast),
                $this->formatDisplay($totalCurrent)

            ];
            $totals[$sectionName]     = $totalCurrent;
            $totalsLast[$sectionName] = $totalLast;
        }

        $kasAkhir = $this->getKasBankSaldo([12110000, 12129999], $selectedMonth);
        $kasAwalCurrent =  $this->getKasBankSaldoAwal([12110000, 12129999], $selectedMonth);
        $kenaikanPenurunan = $kasAkhir - $kasAwalCurrent;

        $kasAkhirLast = $this->getKasBankSaldo([12110000, 12129999], $previousMonth);
        $kasAwalLast =  $this->getKasBankSaldoAwal([12110000, 12129999], $previousMonth);
        $kenaikanPenurunanLast = $kasAkhirLast - $kasAwalLast;


        $result[] = ['', '', ''];
        $result[] = ['ARUS KAS BERSIH DAN SALDO KAS', '', ''];

        $result[] = [
            'Kenaikan/Penurunan Kas Bersih',
            $this->formatDisplay($kenaikanPenurunanLast),
            $this->formatDisplay($kenaikanPenurunan)
        ];

        // Kas Pada Awal Periode
        $result[] = [
            'Kas Pada Awal Periode',
            $this->formatDisplay($kasAwalLast),
            $this->formatDisplay($kasAwalCurrent)
        ];

        // Kas Pada Akhir Periode
        $result[] = [
            'Kas Pada Akhir Periode',
            $this->formatDisplay($kasAkhirLast),
            $this->formatDisplay($kasAkhir)
        ];

        return collect($result);
    }

    private function getKasBankSaldoAwal(array $range, Carbon $date)
    {
        $periodeId = $this->resolvePeriodeId($date);

        $saldoAwal = SaldoAwal::where('periode_id', $periodeId)
            ->whereMonth('tanggal_saldo', $date->month)
            ->whereYear('tanggal_saldo', $date->year)
            ->whereHas('coa', function ($q) use ($range) {
                $q->whereBetween('kode_akun', $range);
            })
            ->selectRaw('COALESCE(SUM(debit),0) as debit, COALESCE(SUM(kredit),0) as kredit')
            ->first();

        return ($saldoAwal->debit ?? 0) - ($saldoAwal->kredit ?? 0);
    }

    private function getKasBankSaldo(array $range, Carbon $date)
    {
        $periodeId = $this->resolvePeriodeId($date);

        $saldoAwal = SaldoAwal::where('periode_id', $periodeId)
            ->whereMonth('tanggal_saldo', $date->month)
            ->whereYear('tanggal_saldo', $date->year)
            ->whereHas('coa', function ($q) use ($range) {
                $q->whereBetween('kode_akun', $range);
            })
            ->selectRaw('COALESCE(SUM(debit),0) as debit, COALESCE(SUM(kredit),0) as kredit')
            ->first();

        $saldoAwalBersih = $saldoAwal->debit - $saldoAwal->kredit;

        $jurnal = Jurnaling::where('periode_id', $periodeId)
            ->whereMonth('tanggal_jurnal', $date->month)
            ->whereYear('tanggal_jurnal', $date->year)
            ->whereHas('coa', function ($q) use ($range) {
                $q->whereBetween('kode_akun', $range);
            })
            ->selectRaw('COALESCE(SUM(debit),0) as total_debit, COALESCE(SUM(kredit),0) as total_kredit')
            ->first();

        return $saldoAwalBersih + ($jurnal->total_debit - $jurnal->total_kredit);
    }

    private function getCustomRanges($key)
    {
        $map = [
            'custom_bunga' => [
                [51110001, 51129999],
                [51210001, 51319999],
                [23410001, 23419999],
                [51610001, 51819999],
                [52110001, 52119999],
                [12210001, 12219999],
            ],
            'custom_dividen' => [
                [51200001, 51209999]
            ],
            'custom_sewa' => [
                [23410003, 23410003],
                [51910001, 51919999],
            ],
            'custom_investasi_lain' => [
                [51510001, 51510999],
            ],
            'custom_pelepasan' => [
                [10010001, 10919999],
            ],
            'custom_penanaman' => [
                [10010001, 10919999],
                [12320001, 12329999],
            ],
            'custom_beban_investasi' => [
                [61010001, 61219999],
                [61610001, 61619999],
            ],
            'custom_beban_operasional' => [
                [61210001, 61219999],
                [70110001, 70419999],
                [23310001, 23319999],
                [23510001, 23517022],
                [12310001, 12319999],
            ],
            'custom_beli_aset_operasional' => [[100, 100]],
            'custom_jual_aset_lain' => [[100, 100]],
            'custom_beli_aset_lain' => [
                [100, 100]
            ],
            'custom_pend_luar_inv' => [[81211001, 81249999]],
            'custom_beban_luar' => [[82220001, 82229999]],
            'custom_iuran_pemberi' => [
                [22111101, 22111199],
                [23611001, 23611099],
                [12220002, 12220002],
            ],
            'custom_iuran_peserta' => [
                [22111001, 22111099],
                [12220001, 12220001],
                [23610001, 23610099],
            ],
            'custom_iuran_tambahan' => [
                [12220003, 12229999],
                [22112001, 22112099],
                [23612001, 23612099],
            ],
            'custom_bunga_iuran' => [[12230001, 12239999]],
            'custom_terima_dana' => [[100, 100]],
            'custom_bayar_dana' => [[22122101, 22122199]],
            'custom_manfaat_pensiun' => [
                [22122001, 22122099],
                [23810001, 23819999],
            ]
        ];

        return $map[$key] ?? [[0, 0]];
    }

    private function sumMultipleRanges(array $ranges, $date, $key = '')
    {
        $total = 0;
        $dateCarbon = $date instanceof \Carbon\Carbon ? $date : \Carbon\Carbon::parse($date);

        Log::info("🔍 START [$key] for date: " . $dateCarbon->format('Y-m'));

        if ($key === 'custom_pelepasan') {
            // Step 1: Ambil saldo dari 1224xxxx (adjustment)
            $adjustQuery = Jurnaling::where('periode_id', $this->periode_id)
                ->whereMonth('tanggal_jurnal', $dateCarbon->month)
                ->whereYear('tanggal_jurnal', $dateCarbon->year)
                ->where('kategori_jurnal', '!=', 'Memorial (Penutup)')
                ->whereHas('coa', function ($q) {
                    $q->whereBetween('kode_akun', [12240001, 12249999]);
                });

            $adjustData = $adjustQuery->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(kredit), 0) as total_kredit')->first();
            $adjustValue = $adjustData->total_debit - $adjustData->total_kredit;

            Log::debug("🧾 [Adjustment 1224xxxx] Date: {$dateCarbon->format('Y-m')} | Debit: {$adjustData->total_debit} | Kredit: {$adjustData->total_kredit} | Adjusted: $adjustValue");

            // Step 2: Ambil jurnal dari range tambahan (pakai kredit seperti sebelumnya)
            $range = $ranges[0];
            $jurnal = Jurnaling::where('periode_id', $this->periode_id)
                ->whereMonth('tanggal_jurnal', $dateCarbon->month)
                ->whereYear('tanggal_jurnal', $dateCarbon->year)
                ->where('kategori_jurnal', '!=', 'Memorial (Penutup)')
                ->whereHas('coa', function ($q) use ($range) {
                    $q->whereBetween('kode_akun', $range);
                })
                ->selectRaw('COALESCE(SUM(kredit), 0) as total_kredit')
                ->first();

            $pelepasanValue = $jurnal->total_kredit;

            Log::debug("🧾 [custom_pelepasan] Kredit: $pelepasanValue");

            $total = $adjustValue - $pelepasanValue;

            // ✅ Pastikan hasil tampil positif
            Log::info("✅ [$key][" . $dateCarbon->format('Y-m') . "] Adjust - Pelepasan: $adjustValue - $pelepasanValue = $total");
            return abs($total); // <-- pastikan positif
        }

        foreach ($ranges as $index => $range) {
            if ($key === 'custom_penanaman') {
                $query = Jurnaling::where('periode_id', $this->periode_id)
                    ->whereMonth('tanggal_jurnal', $dateCarbon->month)
                    ->whereYear('tanggal_jurnal', $dateCarbon->year)
                    ->where('kategori_jurnal', '!=', 'Memorial (Penutup)')
                    ->whereHas('coa', function ($q) use ($range) {
                        $q->whereBetween('kode_akun', $range);
                    });

                $jurnal = $query->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(kredit), 0) as total_kredit')->first();

                $partial = ($index === 0)
                    ? $jurnal->total_debit
                    : -$jurnal->total_kredit;

                Log::debug("🧾 [Special] Key: $key | Index: $index | Range: " . implode('-', $range) . " | Partial: $partial");
            } else {
                $partial = $this->getSaldoAkhir($range, $dateCarbon, $key);
            }

            Log::info("➕ [$key][" . $dateCarbon->format('Y-m') . "] Range: " . implode('-', $range) . " => Partial: $partial");
            $total += $partial;
        }

        // ✅ custom_penanaman harus tampil negatif
        if ($key === 'custom_penanaman') {
            $total = -abs($total);
        }

        Log::info("✅ [$key][" . $dateCarbon->format('Y-m') . "] TOTAL SUM: $total");

        return $total;
    }


    private function getSaldoAkhir(array $range, Carbon $date, $key = '')
    {
        $periodeId = $this->resolvePeriodeId($date);

        $query = Jurnaling::where('periode_id', $periodeId)
            ->whereMonth('tanggal_jurnal', $date->month)
            ->whereYear('tanggal_jurnal', $date->year)
            ->where('kategori_jurnal', '!=', 'Memorial (Penutup)')
            ->whereHas('coa', function ($q) use ($range) {
                if (count($range) === 2) {
                    $q->whereBetween('kode_akun', $range);
                } else {
                    $q->where('kode_akun', $range[0]);
                }
            });

        $jurnal = $query->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(kredit), 0) as total_kredit')->first();

        Log::debug("🧾 Key: $key | Date: {$date->format('Y-m')} | Range: " . implode('-', $range) . " | Debit: {$jurnal->total_debit} | Kredit: {$jurnal->total_kredit}");

        if ($key === 'custom_pelepasan') {
            return $jurnal->total_kredit;
        }

        if ($key === 'custom_penanaman') {
            return $jurnal->total_debit;
        }

        return $jurnal->total_kredit - $jurnal->total_debit;
    }

    private function resolvePeriodeId(Carbon $date)
    {
        return \App\Models\Periode::whereDate('tanggal_awal', '<=', $date->startOfMonth())
            ->whereDate('tanggal_akhir', '>=', $date->endOfMonth())
            ->value('id');
    }

    private function formatDisplay($value)
    {
        return (is_null($value) || $value == 0) ? '-' : round($value, 2);
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
        return 'Laporan Arus Kas';
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
                $sheet->setCellValue('A1', '5');
                $sheet->getStyle('A1')->applyFromArray([
                    'alignment' => ['horizontal' => 'center'],
                    'font' => ['size' => 20],
                ]);

                $titles = [
                    'A2' => 'DANA PENSIUN SEKOLAH KRISTEN',
                    'A3' => 'SINODE GKJ & GKI JAWA TENGAH SALATIGA',
                    'A4' => '(PROGRAM PENSIUM MANFAAT PASTI)',
                    'A5' => 'LAPORAN ARUS KAS',
                    'A6' => 'Per ' . $previousMonth->translatedFormat('F Y') . ' & ' . $selectedMonth->translatedFormat('F Y')
                ];

                // Empty spacing rows
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
                $sheet->getStyle("B8:C$highestRow")->getNumberFormat()
                    ->setFormatCode('#,##0.00;(#,##0.00)');

                for ($row = 8; $row <= $highestRow; $row++) {
                    $val = trim((string)$sheet->getCell("A$row")->getValue());
                    if (preg_match('/^ARUS KAS/', $val)) {
                        $sheet->mergeCells("A$row:C$row");
                        $sheet->setCellValue("A$row", strtoupper($val));
                        $sheet->getStyle("A$row")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 12],
                            'alignment' => ['horizontal' => 'justify'], // atau 'left' + indent
                        ]);
                        $sheet->getStyle("A$row")->getAlignment()->setIndent(-1); // opsional
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

                    if (trim(strtoupper($val)) === 'Kas Pada Akhir Periode') {

                        // Double line BEFORE
                        $sheet->getStyle("B" . ($row - 1) . ":C" . ($row - 1))->applyFromArray([
                            'borders' => [
                                'bottom' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
                                    'color' => ['rgb' => '000000'],
                                ],
                            ],
                        ]);

                        // Bold the text
                        $sheet->getStyle("A$row:C$row")->getFont()->setBold(true);

                        // Double line AFTER
                        $sheet->getStyle("B" . ($row + 1) . ":C" . ($row + 1))->applyFromArray([
                            'borders' => [
                                'top' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
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
