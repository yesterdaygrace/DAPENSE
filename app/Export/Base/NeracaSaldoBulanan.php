<?php

namespace App\Export\Base;

use App\Models\COA;
use App\Models\HeaderCOA;
use App\Models\NeracaSaldo;
use App\Models\SaldoAwal;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NeracaSaldoBulanan implements FromCollection, WithColumnWidths, WithStyles, WithTitle
{
    protected $periode_id;

    protected $month;

    private $headerAccountRanges = [
        '1' => ['min' => 1101, 'max' => 1304],
        '1.1' => ['min' => 1101, 'max' => 1116],
        '1.2' => ['min' => 1201, 'max' => 1210],
        '1.3' => ['min' => 1301, 'max' => 1304],
        '2' => ['min' => 2101, 'max' => 2204],
        '2.1' => ['min' => 2101, 'max' => 2113],
        '2.2' => ['min' => 2201, 'max' => 2204],
        '3' => ['min' => 3101, 'max' => 3203],
        '3.1' => ['min' => 3101, 'max' => 3104],
        '3.2' => ['min' => 3201, 'max' => 3203],
        '4' => ['min' => 4101, 'max' => 4204],
        '4.1' => ['min' => 4101, 'max' => 4108],
        '4.2' => ['min' => 4201, 'max' => 4204],
        '5' => ['min' => 5101, 'max' => 5310],
        '5.1' => ['min' => 5101, 'max' => 5114],
        '5.2' => ['min' => 5201, 'max' => 5210],
        '5.3' => ['min' => 5301, 'max' => 5310],
    ];

    private $headerAccountRangesByIndex = [];

    private $excludedHeaders = [];

    public function __construct($periode_id, $month)
    {
        $this->periode_id = $periode_id;
        $this->month = $month;
        Carbon::setLocale('id');
    }

    public function collection(): Collection
    {
        $selectedMonth = Carbon::parse($this->month.'-01');

        $neracaByCoa = collect();
        NeracaSaldo::where('periode_id', $this->periode_id)
            ->whereMonth('month', $selectedMonth->month)
            ->whereYear('month', $selectedMonth->year)
            ->chunk(100, function ($neracas) use (&$neracaByCoa) {
                foreach ($neracas as $neraca) {
                    $neracaByCoa[$neraca->coa_id] = $neraca;
                }
            });

        $saldoAwalByCoa = collect();
        SaldoAwal::where('periode_id', $this->periode_id)
            ->whereMonth('tanggal_saldo', $selectedMonth->month)
            ->whereYear('tanggal_saldo', $selectedMonth->year)
            ->chunk(100, function ($saldos) use (&$saldoAwalByCoa) {
                foreach ($saldos as $saldo) {
                    $saldoAwalByCoa[$saldo->coa_id] = $saldo;
                }
            });

        $allCoas = collect();
        COA::chunk(100, function ($coas) use (&$allCoas) {
            foreach ($coas as $coa) {
                $allCoas->push($coa);
            }
        });

        $headerCoas = HeaderCOA::with('children')->whereNull('parent_id')->get();

        foreach ($headerCoas->flatten() as $header) {
            $this->processHeader($header, $neracaByCoa, $saldoAwalByCoa, $allCoas);
        }

        $rows = collect([
            ['DANA PENSIUN SEKOLAH KRISTEN'],
            ['SINODE GKJ & GKI JAWA TENGAH SALATIGA'],
            ['(PROGRAM PENSIUN MANFAAT PASTI)'],
            ['NERACA SALDO'],
            ['Periode: '.$selectedMonth->translatedFormat('F Y')],
            [''],
            [''],
            [''],
            ['Kode Perk', 'Nama Perkiraan', 'Saldo Awal', 'Debit', 'Kredit', 'Saldo Akhir'],
        ]);

        foreach ($headerCoas as $header) {
            $this->renderHeader($header, $rows);
        }

        return $rows;
    }

    private function processHeader($header, $neracaByCoa, $saldoAwalByCoa, $allCoas, $index = null)
    {
        $header->total_debit = 0;
        $header->total_kredit = 0;
        $header->total_saldo_awal_debit = 0;
        $header->total_saldo_awal_kredit = 0;
        $header->total_saldo_akhir = 0;
        $header->coas = collect();

        $combinedKey = $header->kode_header.'|'.$header->nama_header;

        $range = $this->headerAccountRanges[$combinedKey]
            ?? $this->headerAccountRanges[$header->kode_header]
            ?? ($index !== null && isset($this->headerAccountRangesByIndex[$header->kode_header][$index])
                ? $this->headerAccountRangesByIndex[$header->kode_header][$index]
                : null);

        if ($range) {
            $coasInRange = $allCoas->filter(function ($coa) use ($range) {
                return $coa->kode_akun >= $range['min'] && $coa->kode_akun <= $range['max'];
            });

            foreach ($coasInRange as $coa) {
                $neraca = $neracaByCoa->get($coa->kode_akun);
                $saldoAwal = $saldoAwalByCoa->get($coa->id);

                $coa->saldo_awal_debit = $saldoAwal->debit ?? 0;
                $coa->saldo_awal_kredit = $saldoAwal->kredit ?? 0;
                $coa->total_debit = $neraca->debit ?? 0;
                $coa->total_kredit = $neraca->kredit ?? 0;

                $coa->saldo_akhir = ($coa->saldo_awal_debit - $coa->saldo_awal_kredit)
                    + ($coa->total_debit - $coa->total_kredit);

                $header->total_debit += $coa->total_debit;
                $header->total_kredit += $coa->total_kredit;
                $header->total_saldo_awal_debit += $coa->saldo_awal_debit;
                $header->total_saldo_awal_kredit += $coa->saldo_awal_kredit;
                $header->total_saldo_akhir += $coa->saldo_akhir;

                $header->coas->push($coa);
            }
        }

        foreach ($header->children as $i => $child) {
            $this->processHeader($child, $neracaByCoa, $saldoAwalByCoa, $allCoas, $i);

            if (! $range) {
                $header->total_debit += $child->total_debit;
                $header->total_kredit += $child->total_kredit;
                $header->total_saldo_awal_debit += $child->total_saldo_awal_debit;
                $header->total_saldo_awal_kredit += $child->total_saldo_awal_kredit;
                $header->total_saldo_akhir += $child->total_saldo_akhir;
            }
        }
    }

    private function renderHeader($header, &$rows, $level = 0)
    {
        $indent = str_repeat(' ', $level * 2);

        $isExcluded = false;
        foreach ($this->excludedHeaders as $excluded) {
            if (
                $header->kode_header === $excluded['kode'] &&
                (! array_key_exists('nama', $excluded) || $header->nama_header === $excluded['nama'])
            ) {
                $isExcluded = true;
                break;
            }
        }

        if ($isExcluded) {
            foreach ($header->children as $child) {
                $this->renderHeader($child, $rows, $level + 1);
            }

            return;
        }

        $overrideToZero = in_array($header->kode_header, ['1', '2', '3', '4', '5']);
        $saldoAwal = $overrideToZero ? 0 : ($header->total_saldo_awal_debit - $header->total_saldo_awal_kredit);
        $saldoAkhir = $overrideToZero ? 0 : $header->total_saldo_akhir;

        if ($saldoAwal == 0 && $header->total_debit == 0 && $header->total_kredit == 0 && $saldoAkhir == 0) {
            foreach ($header->children as $child) {
                $this->renderHeader($child, $rows, $level + 1);
            }

            return;
        }

        $rows->push([
            $indent.$header->kode_header,
            $header->nama_header,
            $this->formatAngka($saldoAwal),
            $this->formatAngka($header->total_debit),
            $this->formatAngka($header->total_kredit),
            $this->formatAngka($saldoAkhir),
        ]);

        $skipCoa = $header->nama_header === 'PEMBELIAN-PENJUALAN AKT.OPRNL';

        if ((strlen($header->kode_header) == 3) && ! $skipCoa) {
            foreach ($header->coas as $coa) {
                if (
                    $coa->saldo_awal_debit == 0 && $coa->saldo_awal_kredit == 0 &&
                    $coa->total_debit == 0 && $coa->total_kredit == 0 &&
                    $coa->saldo_akhir == 0
                ) {
                    continue;
                }

                $rows->push([
                    $indent.'   '.$coa->kode_akun,
                    $indent.'   '.$coa->nama_akun,
                    $this->formatAngka($coa->saldo_awal_debit - $coa->saldo_awal_kredit),
                    $this->formatAngka($coa->total_debit),
                    $this->formatAngka($coa->total_kredit),
                    $this->formatAngka($coa->saldo_akhir),
                ]);
            }
        }

        foreach ($header->children as $child) {
            $this->renderHeader($child, $rows, $level + 1);
        }
    }

    private function formatAngka($value)
    {
        $formatted = number_format(abs($value), 2);

        return $value < 0 ? "($formatted)" : $formatted;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 16,
            'B' => 35,
            'C' => 18,
            'D' => 18,
            'E' => 18,
            'F' => 18,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Merge dan center judul
        foreach ([1, 2, 3, 4, 5] as $row) {
            $sheet->mergeCells("A{$row}:F{$row}");
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        }

        $sheet->getStyle('A1')->getFont()->setSize(14);
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A3')->getFont()->setSize(11);

        // Baris header tabel (baris ke-9)
        $headerRow = 9;

        $sheet->getStyle("A{$headerRow}:F{$headerRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 11],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFDCE6F1'],
            ],
        ]);

        // Border seluruh isi tabel
        $sheet->getStyle("A{$headerRow}:F{$highestRow}")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_HAIR],
            ],
        ]);

        // Alignment isi tabel
        $sheet->getStyle("A{$headerRow}:B{$highestRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

        $sheet->getStyle("C{$headerRow}:F{$highestRow}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->getPageMargins()->setTop(0.75);
        $sheet->getPageMargins()->setBottom(0.75);
        $sheet->getPageMargins()->setLeft(0.25);
        $sheet->getPageMargins()->setRight(0.25);
        $sheet->getPageMargins()->setHeader(0.3);
        $sheet->getPageMargins()->setFooter(0.3);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_FOLIO);

        $protection = $sheet->getProtection();
        $protection->setSheet(true);
        $protection->setPassword('dapense');
    }

    public function title(): string
    {
        return 'Neraca Saldo';
    }
}
