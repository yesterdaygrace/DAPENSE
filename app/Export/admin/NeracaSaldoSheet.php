<?php

namespace App\Export\admin;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class NeracaSaldoSheet implements WithMultipleSheets
{
    protected $periode_id;

    protected $month;

    public function __construct($periode_id, $month)
    {
        $this->periode_id = $periode_id;
        $this->month = $month;
    }

    public function sheets(): array
    {
        return [
            new NeracaSaldoBulanan($this->periode_id, $this->month),
            new LaporanAsetNeto($this->periode_id, $this->month),       // Summary sheet
            new LaporanPerubahanAsetNeto($this->periode_id, $this->month),       // Summary sheet
            new LaporanNeraca($this->periode_id, $this->month),       // Summary sheet
            new LaporanPerhitunganHasilUsaha($this->periode_id, $this->month),       // Summary sheet
            new LaporanArusKas($this->periode_id, $this->month),       // Summary sheet
            new LaporanInvestasi($this->periode_id, $this->month),       // Summary sheet
            new LaporanAnalisaLikuiditas($this->periode_id, $this->month),       // Summary sheet
            new LaporanBulanHasilInvestasi($this->periode_id, $this->month),       // Summary sheet
        ];
    }
}
