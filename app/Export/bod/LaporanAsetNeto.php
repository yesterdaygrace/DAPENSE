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

class LaporanAsetNeto implements WithTitle, FromCollection, WithHeadings, WithEvents, WithColumnWidths
{
    protected $periode_id, $month;
    public static $asetNeto = ['current' => 0, 'last' => 0];

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
                'Piutang lain lain' => [12250001, 12250001],
            ],
            '^ASET OPERASI NILAI BUKU' => [
                'Tanah & Bangunan' =>  [
                    'range' => [13110001, 13129999],
                    'add' => [13210001, 13219999]
                ],
                'Kendaraan' => [
                    'range' => [13140001, 13149999],
                    'add' => [13230001, 13239999]
                ],
                'Peralatan Komputer' => [
                    'range' => [13150001, 13159999],
                    'add' => [13240001, 13249999]
                ],
                'Peralatan Kantor' =>  [
                    'range' => [13130001, 13139999],
                    'add' => [13220001, 13229999]
                ],
                'Akt Op Lain' => [
                    'range' => [13160001, 13169999],
                    'add' => [13250001, 13259999]
                ],
            ],
            '^LIABILITAS' => [
                'Utang Manfaat Pensiun Jatuh Tempo' => [23810001, 23819999],
                'Utang Investasi' => [23210001, 23219999],
                'Beban yang Masih Harus Dibayar' => [23410001, 23419999],
                'Pendapatan Diterima Dimuka' => [23510001, 23519999],
                'Liabilitas Lain' => [23610001, 23619999],
            ]
        ];


        $totals = [];
        $result = [];
        $asetNetoTotals = ['current' => 0, 'last' => 0];

        // Tambahan: untuk hitung "Total Aset Lain Lain"
        $asetLainTotals = ['current' => 0, 'last' => 0];

        foreach ($sections as $section => $accounts) {
            $isTitle = str_starts_with($section, '^');
            $sectionName = strtoupper(str_replace('^', '', $section));
            $isLiabilitas = ($sectionName === 'LIABILITAS');

            if ($isTitle) {
                $result[] = ['Investasi Nilai Buku' => $section, 'Saldo Akhir (Current)' => '', 'Saldo Akhir (Last)' => ''];
            }

            $totalCurrent = $totalLast = 0;

            foreach ($accounts as $name => $config) {
                $range = is_array($config) && isset($config['range']) ? $config['range'] : $config;
                $offset = is_array($config) && isset($config['offset']) ? $config['offset'] : [];
                $add = is_array($config) && isset($config['add']) ? $config['add'] : [];

                $last = $this->getSaldoAkhir($range, $previousMonth, $name, 'Last', 0, $offset, $add);
                $current = $this->getSaldoAkhir($range, $selectedMonth, $name, 'Current', $last, $offset, $add);

                $result[] = [
                    'Investasi Nilai Buku' => $name,
                    'Saldo Akhir (Current)' => $this->formatSaldo($current, $isLiabilitas),
                    'Saldo Akhir (Last)' => $this->formatSaldo($last, $isLiabilitas),
                ];

                $totalCurrent += abs($current);
                $totalLast += abs($last);
            }

            $label = 'Total ' . ucwords(strtolower(str_replace('^', '', $section)));
            $result[] = [
                'Investasi Nilai Buku' => '               ' . $label,
                'Saldo Akhir (Current)' => $this->formatSaldo($totalCurrent, $isLiabilitas),
                'Saldo Akhir (Last)' => $this->formatSaldo($totalLast, $isLiabilitas),
            ];

            // Akumulasi untuk ASET NETO
            if ($isLiabilitas) {
                $asetNetoTotals['current'] -= $totalCurrent;
                $asetNetoTotals['last'] -= $totalLast;
            } else {
                $asetNetoTotals['current'] += $totalCurrent;
                $asetNetoTotals['last'] += $totalLast;

                // Akumulasi untuk TOTAL ASET LAIN LAIN (3 bagian pertama saja)
                if (in_array($sectionName, [
                    'INVESTASI NILAI BUKU',
                    'ASET LANCAR DILUAR INVESTASI',
                    'ASET OPERASI NILAI BUKU'
                ])) {
                    $asetLainTotals['current'] += $totalCurrent;
                    $asetLainTotals['last'] += $totalLast;
                }
            }
        }

        $result[] = [
            'Investasi Nilai Buku' => 'ASET NETO',
            'Saldo Akhir (Current)' => $this->formatSaldo($asetNetoTotals['current']),
            'Saldo Akhir (Last)' => $this->formatSaldo($asetNetoTotals['last']),
        ];

        self::$asetNeto = $asetNetoTotals;

        return collect($result);
    }

    private function getSaldoAkhir(array $range, Carbon $date, string $label, string $pos, $fallback = 0, array $offsetAccounts = [], array $addAccounts = [])
    {
        $periodeId = $this->periode_id;

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

        // Tambahkan dari akun tambahan jika ada
        if (!empty($addAccounts)) {
            $addSaldo = $this->getSaldoAkhir($addAccounts, $date, $label . ' (Add)', $pos, 0);
            $final += $addSaldo;
        }

        // Kurangi offset jika ada
        if (!empty($offsetAccounts)) {
            $offsetSaldo = $this->getSaldoAkhir($offsetAccounts, $date, $label . ' (Offset)', $pos, 0);
            $final -= $offsetSaldo;
        }

        return $final;
    }

    private function formatSaldo($value, $isLiabilitas = false)
    {
        if (!$value) return '-';

        // Always positive for Liabilitas
        if ($isLiabilitas) {
            return number_format(abs($value), 2, ',', '.');
        }

        $formatted = number_format(abs($value), 2, ',', '.');
        return $value < 0 ? "($formatted)" : $formatted;
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
        return 'Laporan Aset Neto';
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
                    'A4' => 'LAPORAN ASET NETO',
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
