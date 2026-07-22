<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasRole;
use App\Models\COA;
use App\Models\Jurnaling;
use App\Models\Periode;
use Livewire\Component;

class NeracaSaldo extends Component
{
    use HasRole;

    public string $periodeId = '';
    public $periodes = [];
    public $saldoEntries = [];
    public float $totalDebit = 0;
    public float $totalKredit = 0;
    public bool $isBalanced = false;

    public function mount($periode = null)
    {
        $this->periodes = Periode::orderBy('tanggal_awal', 'desc')->get();

        if ($periode) {
            $this->periodeId = $periode;
            $this->loadEntries();
        }
    }

    public function loadEntries()
    {
        if (!$this->periodeId) {
            $this->saldoEntries = [];
            return;
        }

        $periode = Periode::findOrFail($this->periodeId);

        $coas = COA::orderBy('kode_akun')->get();
        $entries = [];

        foreach ($coas as $coa) {
            $totals = Jurnaling::where('coa_id', $coa->id)
                ->whereBetween('tanggal_jurnal', [$periode->tanggal_awal, $periode->tanggal_akhir])
                ->selectRaw('COALESCE(SUM(debit),0) as deb, COALESCE(SUM(kredit),0) as kred')
                ->first();

            if ($totals && ($totals->deb > 0 || $totals->kred > 0)) {
                $balance = $totals->deb - $totals->kred;
                $entries[] = (object) [
                    'kode' => $coa->kode_akun,
                    'nama' => $coa->nama_akun,
                    'debit' => $balance > 0 ? $balance : 0,
                    'kredit' => $balance < 0 ? abs($balance) : 0,
                ];
            }
        }

        $this->saldoEntries = $entries;
        $this->totalDebit = collect($entries)->sum('debit');
        $this->totalKredit = collect($entries)->sum('kredit');
        $this->isBalanced = abs($this->totalDebit - $this->totalKredit) < 0.01;
    }

    public function updatedPeriodeId()
    {
        $this->loadEntries();
    }

    public function render()
    {
        return view('livewire.neraca-saldo');
    }
}
