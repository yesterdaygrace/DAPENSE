<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasRole;
use App\Models\COA;
use App\Models\Jurnaling;
use App\Models\Periode;
use Livewire\Component;

class BukuBesar extends Component
{
    use HasRole;

    public string $periodeId = '';
    public string $coaId = '';
    public string $startDate = '';
    public string $endDate = '';

    public $periodes = [];
    public $coas = [];
    public $entries = [];
    public float $totalDebit = 0;
    public float $totalKredit = 0;
    public float $runningBalance = 0;

    public function mount()
    {
        $this->periodes = Periode::orderBy('tanggal_awal', 'desc')->get();
        $this->coas = COA::orderBy('kode_akun')->get();
    }

    public function loadEntries()
    {
        $query = Jurnaling::with('coa')
            ->when($this->periodeId, function ($q) {
                $periode = Periode::find($this->periodeId);
                if ($periode) {
                    $q->whereBetween('tanggal_jurnal', [$periode->tanggal_awal, $periode->tanggal_akhir]);
                }
            })
            ->when($this->coaId, fn($q) => $q->where('coa_id', $this->coaId))
            ->when($this->startDate && $this->endDate, function ($q) {
                $q->whereBetween('tanggal_jurnal', [$this->startDate, $this->endDate]);
            });

        $entries = $query->orderBy('tanggal_jurnal', 'asc')->get();

        $this->entries = $entries;
        $this->totalDebit = $entries->sum('debit');
        $this->totalKredit = $entries->sum('kredit');
        $this->runningBalance = 0;
    }

    public function render()
    {
        return view('livewire.buku-besar');
    }
}
