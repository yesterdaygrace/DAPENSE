<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasRole;
use App\Models\COA;
use App\Models\Jurnaling;
use App\Models\Periode;
use Livewire\Component;
use Livewire\WithPagination;

class JurnalManager extends Component
{
    use HasRole, WithPagination;

    public string $typeFilter = 'km';
    public string $periodeFilter = '';
    public string $search = '';

    public bool $showModal = false;
    public bool $editing = false;
    public $editId = null;
    public $deleteId = null;
    public bool $showDeleteModal = false;

    public array $formData = [
        'tanggal_jurnal' => '',
        'nomor_bukti' => '',
        'keterangan' => '',
        'coa_id' => '',
        'debit' => 0,
        'kredit' => 0,
        'periode_id' => '',
    ];

    public $periodes = [];
    public $coas = [];

    public function mount()
    {
        $this->periodes = Periode::orderBy('tanggal_awal', 'desc')->get();
        $this->coas = COA::orderBy('kode_akun')->get();
    }

    public function updatedTypeFilter()
    {
        $this->resetPage();
    }

    public function updatedPeriodeFilter()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->formData['kategori_jurnal'] = $this->typeFilter;
        $this->formData['nomor_bukti'] = $this->nextBuktiNumber();
        $this->editing = false;
        $this->editId = null;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $entry = Jurnaling::with('coa', 'periode')->findOrFail($id);
        $this->formData = [
            'tanggal_jurnal' => $entry->tanggal_jurnal,
            'nomor_bukti' => $entry->nomor_bukti,
            'keterangan' => $entry->keterangan,
            'coa_id' => (string) $entry->coa_id,
            'debit' => $entry->debit,
            'kredit' => $entry->kredit,
            'periode_id' => (string) $entry->periode_id,
        ];
        $this->editing = true;
        $this->editId = $id;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'formData.tanggal_jurnal' => 'required|date',
            'formData.nomor_bukti' => 'required|string|max:255',
            'formData.coa_id' => 'required|exists:coas,id',
            'formData.debit' => 'required|numeric|min:0',
            'formData.kredit' => 'required|numeric|min:0',
            'formData.periode_id' => 'required|exists:periodes,id',
        ]);

        $data = [
            'tanggal_jurnal' => $this->formData['tanggal_jurnal'],
            'nomor_bukti' => $this->formData['nomor_bukti'],
            'keterangan' => $this->formData['keterangan'],
            'kategori_jurnal' => $this->typeFilter,
            'debit' => $this->formData['debit'],
            'kredit' => $this->formData['kredit'],
            'coa_id' => $this->formData['coa_id'],
            'periode_id' => $this->formData['periode_id'],
        ];

        if ($this->editing && $this->editId) {
            Jurnaling::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Jurnal berhasil diperbarui.');
        } else {
            Jurnaling::create($data);
            session()->flash('success', 'Jurnal berhasil ditambahkan.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteEntry()
    {
        Jurnaling::findOrFail($this->deleteId)->delete();
        session()->flash('success', 'Jurnal berhasil dihapus.');
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    private function resetForm()
    {
        $this->formData = [
            'tanggal_jurnal' => '',
            'nomor_bukti' => '',
            'keterangan' => '',
            'coa_id' => '',
            'debit' => 0,
            'kredit' => 0,
            'periode_id' => '',
        ];
    }

    private function nextBuktiNumber(): string
    {
        $prefix = match ($this->typeFilter) {
            'km' => 'KM', 'kk' => 'KK', 'bm' => 'BM', 'bk' => 'BK',
            'mem' => 'MEM', 'mempenutup' => 'MEM-PEN', default => 'JV',
        };

        $max = Jurnaling::where('kategori_jurnal', $this->typeFilter)
            ->max('nomor_bukti');

        if ($max && preg_match('/(\d+)$/', $max, $m)) {
            return $prefix . '-' . str_pad(((int) $m[1]) + 1, 4, '0', STR_PAD_LEFT);
        }

        return $prefix . '-0001';
    }

    public function render()
    {
        $entries = Jurnaling::with('coa')
            ->where('kategori_jurnal', $this->typeFilter)
            ->when($this->periodeFilter, fn($q) => $q->where('periode_id', $this->periodeFilter))
            ->when($this->search, fn($q) => $q->where('nomor_bukti', 'like', '%' . $this->search . '%'))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('livewire.jurnal-manager', compact('entries'));
    }
}
