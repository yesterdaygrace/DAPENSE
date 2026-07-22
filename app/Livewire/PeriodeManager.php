<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasRole;
use App\Models\Periode;
use Livewire\Component;

class PeriodeManager extends Component
{
    use HasRole;

    public bool $showModal = false;
    public bool $editing = false;
    public $editId = null;
    public $deleteId = null;
    public bool $showDeleteModal = false;

    public array $formData = [
        'nama_periode' => '',
        'tanggal_awal' => '',
        'tanggal_akhir' => '',
        'is_rekap' => false,
    ];

    public function create()
    {
        $this->formData = ['nama_periode' => '', 'tanggal_awal' => '', 'tanggal_akhir' => '', 'is_rekap' => false];
        $this->editing = false;
        $this->editId = null;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $p = Periode::findOrFail($id);
        $this->formData = [
            'nama_periode' => $p->nama_periode,
            'tanggal_awal' => $p->tanggal_awal,
            'tanggal_akhir' => $p->tanggal_akhir,
            'is_rekap' => (bool) $p->is_rekap,
        ];
        $this->editing = true;
        $this->editId = $id;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'formData.nama_periode' => 'required|string|max:255',
            'formData.tanggal_awal' => 'required|date',
            'formData.tanggal_akhir' => 'required|date|after_or_equal:formData.tanggal_awal',
            'formData.is_rekap' => 'boolean',
        ]);

        $data = $this->formData;

        if ($this->editing && $this->editId) {
            Periode::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Periode berhasil diperbarui.');
        } else {
            Periode::create($data);
            session()->flash('success', 'Periode berhasil ditambahkan.');
        }

        $this->showModal = false;
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deletePeriode()
    {
        Periode::findOrFail($this->deleteId)->delete();
        session()->flash('success', 'Periode berhasil dihapus.');
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function render()
    {
        $periodes = Periode::orderBy('tanggal_awal', 'desc')->get();
        return view('livewire.periode-manager', compact('periodes'));
    }
}
