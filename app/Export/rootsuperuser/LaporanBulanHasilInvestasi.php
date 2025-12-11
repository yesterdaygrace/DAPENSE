<?php

namespace App\Export\rootsuperuser;

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

class LaporanBulanHasilInvestasi implements WithTitle, FromCollection, WithHeadings, WithEvents, WithColumnWidths
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

        $data = [
            'Surat Berharga Negara' => [],
            'Tabungan' => [],
            'Deposito On Call' => [],
            'Deposito Berjangka' => [],
            'Sertifikat Deposito' => [],
            'Sertifikat Bank Indonesia' => [],
            'Saham' => [],
            'Obligasi' => [],
            'Sukuk' => [],
            'Reksa Dana Pasar Uang, Reksa Dana Pendapatan Tetap, Reksa Dana Saham' => [],
            'Reksa Dana Terproteksi, Reksa Dana dengan Penjaminan, Reksa Dana Indeks' => [],
            'Reksadana berbentuk Kontrak Investasi Kolektif Penyertaan Terbatasnya' => [],
            'Reksadana Dana yang Unit Penyertaannya diperdagangakan di Bursa Efek' => [],
            'Efek Beragun Aset dari KIK EBA' => [],
            'Unit Penyertaan Dana Investasi Real Estate berbentuk KIK' => [],
            'Kontrak Opsi Saham' => [],
            'Penempatan langsung pada saham' => [],
            'Tanah' => [],
            'Bangunan' => [],
            'Tanah dan Bangunan' => [],
            'Surat berharga lainnya' => [],
        ];

        $mapping = [
            'Surat Berharga Negara' => [
                'Bunga / Bagi Hasil' => [[51710001, 51719999]],
                'Beban Investasi' => [[61010006]]
            ],
            'Tabungan' => [
                'Bunga / Bagi Hasil' => [[51610001, 51619999]]
            ],
            'Deposito On Call' => [
                'Bunga / Bagi Hasil' => [[51120001, 51129999]]
            ],
            'Deposito Berjangka' => [
                'Bunga / Bagi Hasil' => [[51110001, 51119999]],
                'Beban Investasi' => [[61610001, 61619999]]
            ],
            'Obligasi' => [
                'Bunga / Bagi Hasil' => [[51310001, 51319999]],
                'Beban Investasi' => [[61010003]]
            ],
            'Saham' => [
                'Deviden' => [[51200001, 51209999]],
                'Laba/Rugi Penjualan' => [[51510000, 51519999]],
                'SPI' => [[11200001, 11209999]],
                'Beban Investasi' => [[61010001]]
            ]
        ];

        $rows = [];
        $totalsRaw = [
            'Bunga / Bagi Hasil' => 0,
            'Deviden' => 0,
            'Sewa' => 0,
            'Lainnya' => 0,
            'Laba/Rugi Penjualan' => 0,
            'SPI' => 0,
            'Beban Investasi' => 0,
        ];
        $totalHasilInvestasiBersih = 0;

        foreach ($data as $jenis => $_) {
            $row = ['Jenis Investasi' => $jenis];
            $rowRaw = [];

            foreach (array_keys($totalsRaw) as $kategori) {
                $value = 0;
                $isBeban = $kategori === 'Beban Investasi';

                if (isset($mapping[$jenis][$kategori])) {
                    foreach ($mapping[$jenis][$kategori] as $range) {
                        $value += $this->getSaldo($range, $selectedMonth, !$isBeban);
                    }
                }

                // ✅ Logic: store as negative if Beban Investasi
                $rowRaw[$kategori] = $isBeban ? -1 * $value : $value;

                // ✅ Displayed as positive in Excel
                $row[$kategori] = $this->formatSaldo(abs($value));

                // ✅ Accumulate total
                $totalsRaw[$kategori] += $rowRaw[$kategori];
            }

            // ✅ Net Investment Result
            $hasilInvestasiBersih = array_sum($rowRaw);
            $row['Hasil Investasi Bersih'] = $this->formatSaldo($hasilInvestasiBersih);

            $totalHasilInvestasiBersih += $hasilInvestasiBersih;

            $rows[] = $row;
        }

        // ✅ Total row
        $totalRow = ['Jenis Investasi' => 'TOTAL'];
        foreach ($totalsRaw as $kategori => $value) {
            $totalRow[$kategori] = $this->formatSaldo(abs($value));
        }
        $totalRow['Hasil Investasi Bersih'] = $this->formatSaldo($totalHasilInvestasiBersih);
        $rows[] = $totalRow;

        return collect($rows);
    }


    private function getSaldo(array $range, Carbon $date, bool $treatKreditAsPositiveIfNoDebit = true)
    {
        $periodeId = $this->resolvePeriodeId($date);

        $query = Jurnaling::where('periode_id', $this->periode_id)
            ->whereMonth('tanggal_jurnal', $date->month)
            ->whereYear('tanggal_jurnal', $date->year)
            ->whereHas('coa', function ($q) use ($range) {
                if (count($range) === 2) {
                    $q->whereBetween('kode_akun', $range);
                } else {
                    $q->where('kode_akun', $range[0]);
                }
            });

        $result = $query
            ->selectRaw('COALESCE(SUM(debit), 0) as debit, COALESCE(SUM(kredit), 0) as kredit')
            ->first();

        if ($result->debit == 0 && $treatKreditAsPositiveIfNoDebit) {
            return $result->kredit;
        }

        return $result->debit - $result->kredit;
    }

    private function resolvePeriodeId(Carbon $date)
    {
        return \App\Models\Periode::whereDate('tanggal_awal', '<=', $date->startOfMonth())
            ->whereDate('tanggal_akhir', '>=', $date->endOfMonth())
            ->value('id');
    }


    private function formatSaldo($value)
    {
        if ($value === 0 || $value === null) return '00,00';
        $formatted = number_format(abs($value), 2, ',', '.');
        return $value < 0 ? "($formatted)" : $formatted;
    }

    public function headings(): array
    {
        return [
            'Jenis Investasi',
            'Bunga / Bagi Hasil',
            'Deviden',
            'Sewa',
            'Lainnya',
            'Laba/Rugi Penjualan',
            'SPI',
            'Beban Investasi',
            'Hasil Investasi Bersih'
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 40, 'B' => 20, 'C' => 20, 'D' => 20, 'E' => 20, 'F' => 20, 'G' => 20, 'H' => 20, 'I' => 25];
    }

    public function title(): string
    {
        return 'Laporan Hasil Investasi';
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
                    'A4' => 'LAPORAN HASIL INVESTASI BULANAN',
                    'A5' => 'Per ' . $previousMonth->translatedFormat('F Y') . ' s/d ' . $selectedMonth->translatedFormat('F Y')
                ];

                $sheet->setCellValue('A6', '');
                $sheet->setCellValue('A7', '');

                foreach ($titles as $cell => $text) {
                    $sheet->mergeCells($cell . ':I' . substr($cell, 1));
                    $sheet->setCellValue($cell, $text);
                    $sheet->getStyle($cell)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 12],
                        'alignment' => ['horizontal' => 'center'],
                    ]);
                }

                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A8:I$highestRow")->getAlignment()->setHorizontal('center');
                $sheet->getStyle("A8:I$highestRow")->getFont()->setSize(10);
                $sheet->getStyle("A$highestRow:I$highestRow")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E2EFDA']
                    ]
                ]);

                $footerRow = $highestRow + 5;
                $endOfMonthDate = $selectedMonth->copy()->endOfMonth();

                $sheet->mergeCells("A$footerRow:I$footerRow");
                $sheet->setCellValue("A$footerRow", 'Salatiga, ' . $endOfMonthDate->translatedFormat('d F Y'));
                $sheet->getStyle("A$footerRow")->applyFromArray([
                    'alignment' => ['horizontal' => 'center'],
                    'font' => ['size' => 11],
                ]);
                $footerRow++;

                $sheet->mergeCells("A$footerRow:I$footerRow");
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
                    $sheet->setCellValue("C$footerRow", $left->nama_otorisator);
                    $sheet->getStyle("C$footerRow")->applyFromArray([
                        'font' => ['underline' => true, 'size' => 11],
                        'alignment' => ['horizontal' => 'center'],
                    ]);
                }
                if ($right) {
                    $sheet->setCellValue("F$footerRow", $right->nama_otorisator);
                    $sheet->getStyle("F$footerRow")->applyFromArray([
                        'font' => ['underline' => true, 'size' => 11],
                        'alignment' => ['horizontal' => 'center'],
                    ]);
                }
                $footerRow++;

                if ($left) {
                    $sheet->setCellValue("C$footerRow", $left->jabatan_otorisator);
                }
                if ($right) {
                    $sheet->setCellValue("F$footerRow", $right->jabatan_otorisator);
                }
                $footerRow += 5;

                $sheet->getStyle("C" . ($highestRow + 3) . ":F$footerRow")->applyFromArray([
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
