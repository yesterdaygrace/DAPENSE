<?php

namespace App\Export\bod;

use App\Models\Jurnaling;
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

class LaporanPerhitunganHasilUsaha implements WithTitle, FromCollection, WithHeadings, WithEvents, WithColumnWidths
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
                    'Pendapatan Investasi Lain'
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
                $this->formatSaldo($totalCurrent),
                $this->formatSaldo($totalLast),
            ];

            $totals[$sectionName] = [$totalCurrent, $totalLast];

            if ($sectionName === 'BEBAN INVESTASI') {
                $hasilInvestasiCurrent = $totals['PENDAPATAN INVESTASI'][0] - $totals['BEBAN INVESTASI'][0];
                $hasilInvestasiLast = $totals['PENDAPATAN INVESTASI'][1] - $totals['BEBAN INVESTASI'][1];
                $result[] = [
                    'HASIL USAHA INVESTASI',
                    $this->formatSaldo($hasilInvestasiCurrent),
                    $this->formatSaldo($hasilInvestasiLast),
                ];
            }

            if ($sectionName === 'PENDAPATAN DAN BEBAN LAIN-LAIN') {
                $hasilUsahaSetelahPajakCurrent = $hasilInvestasiCurrent - $totals['BEBAN OPERASIONAL'][0];
                $hasilUsahaSetelahPajakLast = $hasilInvestasiLast - $totals['BEBAN OPERASIONAL'][1];
                $result[] = [
                    'HASIL USAHA SETELAH PAJAK',
                    $this->formatSaldo($hasilUsahaSetelahPajakCurrent),
                    $this->formatSaldo($hasilUsahaSetelahPajakLast),
                ];

                $hasilUsahaSebelumPajakCurrent = $hasilUsahaSetelahPajakCurrent + $totals['PENDAPATAN DAN BEBAN LAIN-LAIN'][0];
                $hasilUsahaSebelumPajakLast = $hasilUsahaSetelahPajakLast + $totals['PENDAPATAN DAN BEBAN LAIN-LAIN'][1];
                $result[] = [
                    'HASIL USAHA SEBELUM PAJAK',
                    $this->formatSaldo($hasilUsahaSebelumPajakCurrent),
                    $this->formatSaldo($hasilUsahaSebelumPajakLast),
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
        $jurnal = Jurnaling::where('periode_id', $this->periode_id)
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

    private function formatSaldo($value)
    {

        if ($value === null || $value == 0) {
            return '-'; // kosong jadi strip
        }

        if ($value < 0) {
            return '(' . number_format(abs($value), 2, ',', '.') . ')';
        }

        return number_format($value, 2, ',', '.');
    }

    public function headings(): array
    {
        return ['ASET', 'Saldo Akhir (Current)', 'Saldo Akhir (Last)'];
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
                $sheet = $event->sheet;
                $selectedMonth = Carbon::parse($this->month . '-01');
                $previousMonth = $selectedMonth->copy()->subMonth();

                $sheet->insertNewRowBefore(1, 7);

                $titles = [
                    'A1' => 'DANA PENSIUN SEKOLAH KRISTEN',
                    'A2' => 'SINODE GKJ & GKI JAWA TENGAH SALATIGA',
                    'A3' => '(PROGRAM PENSIUM MANFAAT PASTI)',
                    'A4' => 'LAPORAN PERHITUNGAN HASIL USAHA',
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
                    $val = trim((string)$sheet->getCell("A$row")->getValue());

                    if (stripos($val, 'Total') !== false || stripos($val, 'HASIL') !== false) {
                        $sheet->getStyle("A$row:C$row")->getFont()->setBold(true);
                    }
                }
            }
        ];
    }
}
