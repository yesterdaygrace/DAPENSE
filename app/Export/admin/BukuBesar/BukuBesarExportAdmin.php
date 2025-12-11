<?php

namespace App\Export\admin\BukuBesar;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithTitle,
    WithMapping,
    WithEvents,
    ShouldAutoSize,
    WithCustomStartCell
};
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;

class BukuBesarExportAdmin implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithEvents,
    ShouldAutoSize,
    WithCustomStartCell
{
    protected $entries;
    protected $tahun;
    protected $kodeAkun;
    protected $namaAkun;
    protected $bulan;

    public function __construct(Collection $entries, $tahun, $kodeAkun, $namaAkun, $bulan)
    {
        $this->entries   = $entries;
        $this->tahun     = $tahun;
        $this->kodeAkun  = $kodeAkun;
        $this->namaAkun  = $namaAkun;
        $this->bulan     = $bulan;
        Carbon::setLocale('id');
    }

    public function title(): string
    {
        return 'Buku Besar';
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nomor Bukti',
            'Deskripsi',
            'Debit',
            'Credit',
            'Balance'
        ];
    }

    // Heading tabel dimulai dari baris 6
    public function startCell(): string
    {
        return 'A6';
    }

    public function collection()
    {
        $collection = $this->entries;

        $lastSaldo = $collection->last()['running_total'] ?? 0;

        $collection->push([
            'tanggal'       => '',
            'nomor_bukti'   => '',
            'keterangan'    => '',
            'debit'         => '',
            'kredit'        => '',
            'running_total' => $lastSaldo
        ]);

        return $collection;
    }

    public function map($row): array
    {
        return [
            $row['tanggal'],
            $row['nomor_bukti'],
            $row['keterangan'],
            $row['debit'],
            $row['kredit'],
            $row['running_total']
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                // ================================
                // 🔵 Header Informasi (baris 1–4)
                // ================================
                $sheet->mergeCells('A1:F1');
                $sheet->setCellValue('A1', 'Perincian Buku Besar');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ]
                ]);

                $sheet->mergeCells('A2:F2');
                $sheet->setCellValue('A2', 'Periode: ' . $this->tahun);
                $sheet->getStyle('A2')->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ]
                ]);

                $sheet->setCellValue('A4', 'Account: ' . $this->kodeAkun);
                $sheet->setCellValue('C4', 'Keterangan: ' . $this->namaAkun);
                $sheet->setCellValue('A5', 'Bulan: ' . $this->bulan);

                // ================================
                // 🔵 Styling Tabel
                // ================================
                $highestRow   = $sheet->getHighestRow();
                $dataStartRow = 7; // data dimulai dari baris 7

                // Header tabel bold + center (baris 6)
                $sheet->getStyle("A6:F6")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ]
                ]);

                // Format angka ribuan
                $sheet->getStyle("D{$dataStartRow}:F{$highestRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##0.00');

                // Border tabel
                $sheet->getStyle("A6:F{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ]
                    ]
                ]);

                // Styling baris total
                $sheet->getStyle("A{$highestRow}:F{$highestRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '000000']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => 'solid',
                        'color' => ['rgb' => 'F2E8C9']
                    ]
                ]);

                $sheet->setCellValue("E{$highestRow}", "TOTAL:");
                $sheet->getStyle("E{$highestRow}")->applyFromArray([
                    'font' => ['bold' => true]
                ]);
                $protection = $sheet->getProtection();
                $protection->setSheet(true);
                $protection->setPassword('dapense');
            }

        ];
    }
}
