<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasRole;
use App\Models\COA;
use App\Models\Jurnaling;
use App\Models\NeracaSaldo;
use App\Models\Periode;
use App\Models\SaldoAwal;
use Livewire\Component;

class Posting extends Component
{
    use HasRole;

    public string $periodeId = '';
    public $periodes = [];
    public int $entryCount = 0;
    public bool $showConfirmModal = false;
    public string $postingAction = '';

    public function mount()
    {
        if (!$this->canAccess('posting')) {
            return;
        }

        $this->periodes = Periode::orderBy('tanggal_awal', 'desc')->get();
    }

    public function updatedPeriodeId()
    {
        if ($this->periodeId) {
            $this->entryCount = Jurnaling::where('periode_id', $this->periodeId)->count();
        } else {
            $this->entryCount = 0;
        }
    }

    public function showPost()
    {
        $this->postingAction = 'post';
        $this->showConfirmModal = true;
    }

    public function showUnpost()
    {
        $this->postingAction = 'unpost';
        $this->showConfirmModal = true;
    }

    public function executeAction()
    {
        if (!$this->periodeId) {
            session()->flash('error', 'Pilih periode terlebih dahulu.');
            $this->showConfirmModal = false;
            return;
        }

        $periode = Periode::findOrFail($this->periodeId);

        if ($this->postingAction === 'post') {
            $entries = Jurnaling::where('periode_id', $this->periodeId)->get();
            $coas = COA::all();

            foreach ($coas as $coa) {
                $totals = $entries->where('coa_id', $coa->id);
                if ($totals->isEmpty()) continue;

                $totalDebit = $totals->sum('debit');
                $totalKredit = $totals->sum('kredit');
                $balance = $totalDebit - $totalKredit;

                NeracaSaldo::updateOrCreate(
                    ['coa_id' => $coa->id, 'periode_id' => $this->periodeId],
                    [
                        'debit' => $totalDebit,
                        'kredit' => $totalKredit,
                        'balance' => $balance,
                    ]
                );
            }

            $periode->update(['is_rekap' => true]);
            session()->flash('success', 'Posting berhasil. Neraca saldo sudah dihitung.');
        } else {
            NeracaSaldo::where('periode_id', $this->periodeId)->delete();
            $periode->update(['is_rekap' => false]);
            session()->flash('success', 'Unpost berhasil. Neraca saldo sudah dihapus.');
        }

        $this->showConfirmModal = false;
        $this->postingAction = '';
    }

    public function render()
    {
        $periodeStatus = null;
        if ($this->periodeId) {
            $periodeStatus = Periode::find($this->periodeId);
        }

        return view('livewire.posting', compact('periodeStatus'));
    }
}
