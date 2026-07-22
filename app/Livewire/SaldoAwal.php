<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasRole;
use App\Models\COA;
use App\Models\Periode;
use App\Models\SaldoAwal as SaldoAwalModel;
use Livewire\Component;

class SaldoAwal extends Component
{
    use HasRole;

    public string $periodeFilter = '';
    public $periodes = [];
    public $coas = [];

    public bool $showModal = false;
    public bool $editing = false;
    public $editId = null;
    public $deleteId = null;
    public bool $showDeleteModal = false;

    public array $formData = [
        'coa_id' => '',
        'tanggal_saldo' => '',
        'periode_id' => '',
        'debit' => 0,
    ];

    public function mount()
    {
        $this->loadPeriodes();
        $this->coas = COA::orderBy('kode_akun')->get();
    }

    private function loadPeriodes()
    {
        $this->periodes = Periode::orderBy('tanggal_awal', 'desc')->get();
    }

    public function create()
    {
        $this->formData = ['coa_id' => '', 'tanggal_saldo' => '', 'periode_id' => '', 'debit' => 0];
        $this->editing = false;
        $this->editId = null;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $saldo = SaldoAwalModel::findOrFail($id);
        $this->formData = [
            'coa_id' => (string) $saldo->coa_id,
            'tanggal_saldo' => $saldo->tanggal_saldo,
            'periode_id' => (string) $saldo->periode_id,
            'debit' => $saldo->debit,
        ];
        $this->editing = true;
        $this->editId = $id;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'formData.coa_id' => 'required|exists:coas,id',
            'formData.tanggal_saldo' => 'required|date',
            'formData.periode_id' => 'required|exists:periodes,id',
            'formData.debit' => 'required|numeric',
        ]);

        $periode = Periode::find($this->formData['periode_id']);
        $tanggalSaldo = strtotime($this->formData['tanggal_saldo']);
        $tanggalAwal = strtotime($periode->tanggal_awal);
        $tanggalAkhir = strtotime($periode->tanggal_akhir);

        if ($tanggalSaldo < $tanggalAwal || $tanggalSaldo > $tanggalAkhir) {
            session()->flash('error', 'Tanggal saldo harus dalam rentang periode');
            return;
        }

        $data = [
            'coa_id' => $this->formData['coa_id'],
            'tanggal_saldo' => $this->formData['tanggal_saldo'],
            'periode_id' => $this->formData['periode_id'],
            'debit' => $this->formData['debit'],
            'kredit' => 0,
        ];

        if ($this->editing && $this->editId) {
            SaldoAwalModel::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Saldo awal berhasil diperbarui.');
        } else {
            SaldoAwalModel::create($data);
            session()->flash('success', 'Saldo awal berhasil ditambahkan.');
        }

        $this->showModal = false;
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteEntry()
    {
        SaldoAwalModel::findOrFail($this->deleteId)->delete();
        session()->flash('success', 'Saldo awal berhasil dihapus.');
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function render()
    {
        $saldoAwals = SaldoAwalModel::with('coa', 'periode')
            ->when($this->periodeFilter, fn($q) => $q->where('periode_id', $this->periodeFilter))
            ->get();

        return view('livewire.saldo-awal', compact('saldoAwals'));
    }
}
