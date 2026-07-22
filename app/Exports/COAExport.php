<?php

namespace App\Exports;

use App\Models\COA;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class COAExport implements FromCollection, WithColumnWidths, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $includeHeaders;
    protected $includeAudit;

    public function __construct(bool $includeHeaders = true, bool $includeAudit = false)
    {
        $this->includeHeaders = $includeHeaders;
        $this->includeAudit = $includeAudit;
    }

    public function collection()
    {
        $query = COA::with('headerCoa')->orderBy('kode_akun', 'asc');

        return $query->get();
    }

    public function headings(): array
    {
        $headings = [
            'Kode Akun',
            'Nama Akun',
            'Kategori',
            'Saldo Normal',
            'Level',
        ];

        if ($this->includeHeaders) {
            $headings[] = 'Header';
        }

        if ($this->includeAudit) {
            $headings[] = 'Created At';
            $headings[] = 'Updated At';
        }

        return $headings;
    }

    public function map($coa): array
    {
        $row = [
            $coa->kode_akun,
            $coa->nama_akun,
            $coa->kategori,
            $coa->saldo_normal,
            $coa->level,
        ];

        if ($this->includeHeaders) {
            $row[] = $coa->headerCoa->nama_header ?? '';
        }

        if ($this->includeAudit) {
            $row[] = $coa->created_at?->format('d-m-Y H:i:s') ?? '';
            $row[] = $coa->updated_at?->format('d-m-Y H:i:s') ?? '';
        }

        return $row;
    }

    public function title(): string
    {
        return 'Chart of Accounts';
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('1:1')->getFont()->setBold(true);

        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn();

        $sheet->getStyle("A1:{$lastCol}{$lastRow}")
            ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 40,
            'C' => 15,
            'D' => 15,
            'E' => 10,
            'F' => 30,
        ];
    }
}
