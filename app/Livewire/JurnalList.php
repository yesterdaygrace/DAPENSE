<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasRole;
use App\Models\Jurnaling;
use App\Models\Periode;
use Livewire\Component;
use Livewire\WithPagination;

class JurnalList extends Component
{
    use HasRole, WithPagination;

    public string $search = '';
    public string $typeFilter = '';
    public string $periodeFilter = '';
    public $periodes = [];

    public function mount()
    {
        $this->periodes = Periode::orderBy('tanggal_awal', 'desc')->get();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedTypeFilter()
    {
        $this->resetPage();
    }

    public function updatedPeriodeFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $entries = Jurnaling::with('coa')
            ->when($this->typeFilter, fn($q) => $q->where('kategori_jurnal', $this->typeFilter))
            ->when($this->periodeFilter, fn($q) => $q->where('periode_id', $this->periodeFilter))
            ->when($this->search, fn($q) => $q->where(function($q) {
                $q->where('nomor_bukti', 'like', '%' . $this->search . '%')
                  ->orWhere('keterangan', 'like', '%' . $this->search . '%');
            }))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('livewire.jurnal-list', compact('entries'));
    }
}
