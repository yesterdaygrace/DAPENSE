<?php

namespace App\Export\rootsuperuser\Jurnaling;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class JurnalingSheet implements WithMultipleSheets
{
    protected $month;
    protected $periodeId;

    public function __construct($month, $periodeId)
    {
        $this->month = $month;
        $this->periodeId = $periodeId;
    }

    public function sheets(): array
    {
        return [
            'Semua Jurnal'   => new JurnalingSheetExport($this->month, $this->periodeId, null),
            'Kas Masuk'      => new JurnalingSheetExport($this->month, $this->periodeId, 'kas masuk'),
            'Kas Keluar'     => new JurnalingSheetExport($this->month, $this->periodeId, 'kas keluar'),
            'Bank Masuk'     => new JurnalingSheetExport($this->month, $this->periodeId, 'bank masuk'),
            'Bank Keluar'    => new JurnalingSheetExport($this->month, $this->periodeId, 'bank keluar'),
            'Memorial'       => new JurnalingSheetExport($this->month, $this->periodeId, 'memorial'),
        ];
    }
}
