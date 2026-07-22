<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasRole;
use App\Models\Otorisator;
use Livewire\Component;

class OtorisatorManager extends Component
{
    use HasRole;

    public bool $showModal = false;
    public bool $editing = false;
    public $editId = null;
    public $deleteId = null;
    public bool $showDeleteModal = false;

    public array $formData = [
        'nama_otorisator' => '',
        'jabatan_otorisator' => '',
    ];

    public function create()
    {
        $this->formData = ['nama_otorisator' => '', 'jabatan_otorisator' => ''];
        $this->editing = false;
        $this->editId = null;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $o = Otorisator::findOrFail($id);
        $this->formData = [
            'nama_otorisator' => $o->nama_otorisator,
            'jabatan_otorisator' => $o->jabatan_otorisator,
        ];
        $this->editing = true;
        $this->editId = $id;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'formData.nama_otorisator' => 'required|string|max:255',
            'formData.jabatan_otorisator' => 'required|string|max:255',
        ]);

        if ($this->editing && $this->editId) {
            Otorisator::findOrFail($this->editId)->update($this->formData);
            session()->flash('success', 'Otorisator berhasil diperbarui.');
        } else {
            Otorisator::create($this->formData);
            session()->flash('success', 'Otorisator berhasil ditambahkan.');
        }

        $this->showModal = false;
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteOtorisator()
    {
        Otorisator::findOrFail($this->deleteId)->delete();
        session()->flash('success', 'Otorisator berhasil dihapus.');
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function render()
    {
        $otorisators = Otorisator::orderBy('id', 'asc')->get();
        return view('livewire.otorisator-manager', compact('otorisators'));
    }
}
