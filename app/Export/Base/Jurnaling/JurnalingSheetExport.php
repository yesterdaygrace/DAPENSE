<?php

namespace App\Export\Base\Jurnaling;

use App\Models\Jurnaling;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JurnalingSheetExport implements FromCollection, WithColumnWidths, WithCustomStartCell, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $month;

    protected $periodeId;

    protected $kategori;

    public function __construct($month, $periodeId, $kategori = null)
    {
        $this->month = $month;
        $this->periodeId = $periodeId;
        $this->kategori = $kategori;
        Carbon::setLocale('id');
    }

    public function collection()
    {
        $year = substr($this->month, 0, 4);
        $monthNumber = substr($this->month, 5, 2);

        $query = Jurnaling::with('coa')
            ->whereYear('tanggal_jurnal', $year)
            ->whereMonth('tanggal_jurnal', $monthNumber)
            ->where('periode_id', $this->periodeId)
            ->orderBy('tanggal_jurnal', 'asc')
            ->orderBy('nomor_bukti', 'ASC');

        if ($this->kategori) {
            $query->whereRaw('LOWER(kategori_jurnal) = ?', [strtolower($this->kategori)]);
        }

        return $query->get()->map(function ($entry) {
            if (empty(trim($entry->keterangan))) {
                switch (strtolower($entry->kategori_jurnal)) {
                    case 'kas masuk':
                        $entry->keterangan = 'Pemasukan Kas';
                        break;
                    case 'kas keluar':
                        $entry->keterangan = 'Pengeluaran Kas';
                        break;
                    case 'bank masuk':
                        $entry->keterangan = 'Pemasukan Bank';
                        break;
                    case 'bank keluar':
                        $entry->keterangan = 'Pengeluaran Bank';
                        break;
                    case 'memorial':
                        $entry->keterangan = 'Memorial';
                        break;
                    default:
                        $entry->keterangan = '-';
                }
            }

            return $entry;
        });
    }

    public function headings(): array
    {
        return [
            'Tanggal Jurnal',
            'Nomor Bukti',
            'Keterangan',
            'Kategori Jurnal',
            'COA',
            'Debit',
            'Kredit',
        ];
    }

    public function map($entry): array
    {
        return [
            Carbon::parse($entry->tanggal_jurnal)->format('d-m-Y'),
            $entry->nomor_bukti,
            $entry->keterangan,
            $entry->kategori_jurnal,
            $entry->coa->kode_akun . ' - ' . $entry->coa->nama_akun,
            number_format($entry->debit, 2),
            number_format($entry->kredit, 2),
        ];
    }

    public function title(): string
    {
        return $this->kategori === null ? 'Semua Jurnal' : ucwords($this->kategori);
    }

    // Heading dan data mulai dari baris ke-7
    public function startCell(): string
    {
        return 'A7';
    }

    public function styles(Worksheet $sheet)
    {
        // Judul utama
        $sheet->setCellValue('A1', 'DANA PENSIUN SEKOLAH KRISTEN');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Subjudul
        $sheet->setCellValue('A2', 'SINODE GKJ & GKI JAWA TENGAH SALATIGA');
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $bulanTahun = Carbon::createFromFormat('Y-m', $this->month)->translatedFormat('F Y');
        $sheet->setCellValue('A5', "Jurnaling Periode : {$bulanTahun}");
        $sheet->mergeCells('A5:G5');
        $sheet->getStyle('A5')->getFont()->setBold(true);
        $sheet->getStyle('A5')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('A7:G7')->getFont()->setBold(true);

        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A7:G{$lastRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $rows = $sheet->toArray();
        $prevNomor = null;
        foreach ($rows as $index => $row) {
            if ($index < 6) {
                continue;
            }
            $nomor = $row[1];
            if ($nomor !== $prevNomor) {
                $rowIndex = $index + 1;
                $sheet->getStyle("A{$rowIndex}:G{$rowIndex}")
                    ->getBorders()->getTop()->setBorderStyle(Border::BORDER_THICK);
                $sheet->getStyle("A{$rowIndex}:G{$rowIndex}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F2F2F2');
            }
            $prevNomor = $nomor;
        }
        $protection = $sheet->getProtection();
        $protection->setSheet(true);
        $protection->setPassword('dapense');

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 20,
            'C' => 60,
            'D' => 20,
            'E' => 36,
            'F' => 18,
            'G' => 18,
        ];
    }
}
