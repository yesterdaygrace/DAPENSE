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

class LaporanAnalisaLikuiditas implements WithTitle, FromCollection, WithHeadings, WithEvents, WithColumnWidths
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
        $monthNumber = (int) $selectedMonth->format('n');

        $result = [];

        foreach (['^LIQUIDITAS BULANAN', '^LIQUIDITAS AKUMULATIF'] as $sectionTitle) {
            $result[] = ['Investasi Nilai Buku' => $sectionTitle, 'Saldo Akhir (Current)' => '', 'Saldo Akhir (Last)' => ''];

            $isAkumulatif = $sectionTitle === '^LIQUIDITAS AKUMULATIF';
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
                'Saldo Akhir (Last)' => $this->formatSaldo($sectionTitle === '^LIQUIDITAS BULANAN' ? $this->getAsetLancarLast($asetLancarItems, $previousMonth) : $asetLancarTotal),
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
                'Saldo Akhir (Last)' => $this->formatSaldo($sectionTitle === '^LIQUIDITAS BULANAN' ? $this->getBiayaLast($biayaItems, $previousMonth) : $biayaTotal),
                'Saldo Akhir (Current)' => $this->formatSaldo($biayaTotal),
            ];

            if ($isAkumulatif) {
                // JUMLAH DISETAHUNKAN = total biaya / 12 * (bulan ke-n - 1)
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
            } elseif ($sectionTitle === '^LIQUIDITAS BULANAN') {
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
        return \App\Models\Periode::whereDate('tanggal_awal', '<=', $date->startOfMonth())
            ->whereDate('tanggal_akhir', '>=', $date->endOfMonth())
            ->value('id');
    }

    private function formatSaldo($value)
    {
        if (!$value) return '-';
        $formatted = number_format(abs($value), 0, ',', '.');
        return $value < 0 ? "($formatted)" : $formatted;
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
        return 'Laporan Analisa Likuiditas';
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
                    'A4' => 'LAPORAN ANALISA LIKUIDITAS',
                    'A5' => 'Per ' . $previousMonth->translatedFormat('F Y') . ' & ' . $selectedMonth->translatedFormat('F Y')
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
                    if (preg_match('/^TOTAL|^TINGKAT LIQUIDITAS|^JUMLAH DISETAHUNKAN/', $val)) {
                        $sheet->getStyle("A$row:C$row")->getFont()->setBold(true);
                    }
                }
                $protection = $sheet->getProtection();
                $protection->setSheet(true);
                $protection->setPassword('dapense');
            }
        ];
    }
}
