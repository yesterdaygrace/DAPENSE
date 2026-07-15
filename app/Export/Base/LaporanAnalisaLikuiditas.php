<?php

namespace App\Export\Base;

use App\Models\Jurnaling;
use App\Models\Otorisator;
use App\Models\Periode;
use App\Models\SaldoAwal;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class LaporanAnalisaLikuiditas implements FromCollection, WithColumnWidths, WithEvents, WithHeadings, WithTitle
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
        $monthNumber = (int) $selectedMonth->format('n');

        $result = [];

        foreach (['LIQUIDITAS BULANAN', 'LIQUIDITAS AKUMULATIF'] as $sectionTitle) {
            $result[] = ['Investasi Nilai Buku' => $sectionTitle, 'Saldo Akhir (Current)' => '', 'Saldo Akhir (Last)' => ''];

            $isAkumulatif = $sectionTitle === 'LIQUIDITAS AKUMULATIF';
            $sectionTotalCurrent = $sectionTotalLast = 0;
            $asetLancarTotal = $biayaTotal = 0;

            // ASET LANCAR
            $asetLancarItems = [
                'Tabungan' => [10610001, 10619999],
                'Kas & Bank' => [12110000, 12129999],
                'Deposito On Call' => [10020000, 10029999],
            ];

            $result[] = ['Investasi Nilai Buku' => '   ASET LANCAR', 'Saldo Akhir (Current)' => '', 'Saldo Akhir (Last)' => ''];
            foreach ($asetLancarItems as $name => $range) {
                $current = $this->getSaldoAkhir($range, $selectedMonth);
                $last = $this->getSaldoAkhir($range, $previousMonth);
                $result[] = [
                    'Investasi Nilai Buku' => $name,
                    'Saldo Akhir (Last)' => $this->formatSaldo($last),
                    'Saldo Akhir (Current)' => $this->formatSaldo($current),
                ];
                $asetLancarTotal += abs($current);
            }
            $result[] = [
                'Investasi Nilai Buku' => '        Total ASET LANCAR',
                'Saldo Akhir (Last)' => $this->formatSaldo($sectionTitle === 'LIQUIDITAS BULANAN' ? $this->getAsetLancarLast($asetLancarItems, $previousMonth) : $asetLancarTotal),
                'Saldo Akhir (Current)' => $this->formatSaldo($asetLancarTotal),
            ];

            // BIAYA INVESTASI DAN OPERASIONAL
            $biayaItems = [
                'Beban Investasi' => [61010001, 61619999],
                'Beban Operasional' => [[61210001, 61319999], [70111001, 70412999]],
                'Manfaat Pensiun' => [22122001, 22122099],
            ];

            $result[] = ['Investasi Nilai Buku' => '   BIAYA INVESTASI DAN OPERASIONAL', 'Saldo Akhir (Current)' => '', 'Saldo Akhir (Last)' => ''];
            foreach ($biayaItems as $name => $ranges) {
                $ranges = is_array($ranges[0]) ? $ranges : [$ranges];
                $current = $last = 0;
                foreach ($ranges as $range) {
                    $current += $this->getSaldoAkhir($range, $selectedMonth);
                    $last += $this->getSaldoAkhir($range, $previousMonth);
                }
                $result[] = [
                    'Investasi Nilai Buku' => $name,
                    'Saldo Akhir (Last)' => $this->formatSaldo($last),
                    'Saldo Akhir (Current)' => $this->formatSaldo($current),
                ];
                $biayaTotal += abs($current);
            }

            $result[] = [
                'Investasi Nilai Buku' => '        Total BIAYA INVESTASI DAN OPERASIONAL',
                'Saldo Akhir (Last)' => $this->formatSaldo($sectionTitle === 'LIQUIDITAS BULANAN' ? $this->getBiayaLast($biayaItems, $previousMonth) : $biayaTotal),
                'Saldo Akhir (Current)' => $this->formatSaldo($biayaTotal),
            ];

            if ($isAkumulatif) {
                $jumlahDisetahunkan = ($biayaTotal / 12) * ($monthNumber - 1);
                $result[] = [
                    'Investasi Nilai Buku' => 'JUMLAH DISETAHUNKAN',
                    'Saldo Akhir (Last)' => '-',
                    'Saldo Akhir (Current)' => $this->formatSaldo($jumlahDisetahunkan),
                ];
                $result[] = [
                    'Investasi Nilai Buku' => 'TOTAL JUMLAH DISETAHUNKAN',
                    'Saldo Akhir (Last)' => '-',
                    'Saldo Akhir (Current)' => '-',
                ];
                $rasio = $jumlahDisetahunkan != 0 ? $asetLancarTotal / $jumlahDisetahunkan : 0;
                $result[] = [
                    'Investasi Nilai Buku' => 'TINGKAT LIQUIDITAS',
                    'Saldo Akhir (Last)' => '-',
                    'Saldo Akhir (Current)' => number_format($rasio, 2),
                ];
            } elseif ($sectionTitle === 'LIQUIDITAS BULANAN') {
                $rasio = $biayaTotal != 0 ? $asetLancarTotal / $biayaTotal : 0;
                $result[] = [
                    'Investasi Nilai Buku' => 'Rasio Aset Lancar terhadap Biaya',
                    'Saldo Akhir (Last)' => '-',
                    'Saldo Akhir (Current)' => number_format($rasio, 2),
                ];
            }

            $result[] = [
                'Investasi Nilai Buku' => 'TOTAL ' . str_replace('^', '', $sectionTitle),
                'Saldo Akhir (Last)' => $this->formatSaldo($asetLancarTotal + $biayaTotal),
                'Saldo Akhir (Current)' => $this->formatSaldo($asetLancarTotal + $biayaTotal),
            ];
        }

        return collect($result);
    }

    private function getAsetLancarLast(array $items, Carbon $date)
    {
        $total = 0;
        foreach ($items as $range) {
            $total += abs($this->getSaldoAkhir($range, $date));
        }

        return $total;
    }

    private function getBiayaLast(array $items, Carbon $date)
    {
        $total = 0;
        foreach ($items as $ranges) {
            $ranges = is_array($ranges[0]) ? $ranges : [$ranges];
            foreach ($ranges as $range) {
                $total += abs($this->getSaldoAkhir($range, $date));
            }
        }

        return $total;
    }

    private function getSaldoAkhir(array $range, Carbon $date)
    {
        $periodeId = $this->resolvePeriodeId($date);

        $saldoAwal = SaldoAwal::where('periode_id', $periodeId)
            ->whereMonth('tanggal_saldo', $date->month)
            ->whereYear('tanggal_saldo', $date->year)
            ->whereHas('coa', function ($q) use ($range) {
                $q->whereBetween('kode_akun', $range);
            })
            ->selectRaw('COALESCE(SUM(debit), 0) as debit, COALESCE(SUM(kredit), 0) as kredit')
            ->first();

        $jurnal = Jurnaling::where('periode_id', $periodeId)
            ->whereMonth('tanggal_jurnal', $date->month)
            ->whereYear('tanggal_jurnal', $date->year)
            ->whereHas('coa', function ($q) use ($range) {
                $q->whereBetween('kode_akun', $range);
            })
            ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(kredit), 0) as total_kredit')
            ->first();

        return ($saldoAwal->debit - $saldoAwal->kredit) + ($jurnal->total_debit - $jurnal->total_kredit);
    }

    private function resolvePeriodeId(Carbon $date)
    {
        return Periode::whereDate('tanggal_awal', '<=', $date->startOfMonth())
            ->whereDate('tanggal_akhir', '>=', $date->endOfMonth())
            ->value('id');
    }

    private function formatSaldo($value)
    {
        if (! $value) {
            return '-';
        }
        $formatted = number_format(abs($value), 0, ',', '.');

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
        return 'Laporan Analisa Likuiditas';
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
                    'A4' => 'LAPORAN ANALISA LIKUIDITAS',
                    'A5' => 'Per ' . $previousMonth->translatedFormat('F Y') . ' & ' . $selectedMonth->translatedFormat('F Y'),
                ];

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
                    $val = trim((string) $sheet->getCell("A$row")->getValue());
                    if (preg_match('/^TOTAL|^TINGKAT LIQUIDITAS|^JUMLAH DISETAHUNKAN/', $val)) {
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
