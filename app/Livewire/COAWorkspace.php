<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasRole;
use App\Models\COA;
use App\Models\HeaderCOA;
use Livewire\Component;
use Livewire\WithPagination;

class COAWorkspace extends Component
{
    use HasRole, WithPagination;

    public string $activeTab = 'accounts';
    public string $search = '';

    // Account form
    public bool $showModal = false;
    public bool $editing = false;
    public $editId = null;
    public $deleteId = null;
    public bool $showDeleteModal = false;
    public array $formData = [
        'kode_akun' => '',
        'nama_akun' => '',
        'kategori' => '',
        'saldo_normal' => 'Debit',
        'level' => 1,
        'header_coa_id' => '',
    ];

    // Header form
    public bool $showHeaderModal = false;
    public bool $editingHeader = false;
    public $headerEditId = null;
    public $headerDeleteId = null;
    public bool $showHeaderDeleteModal = false;
    public array $headerForm = [
        'kode_header' => '',
        'nama_header' => '',
        'level' => 1,
        'parent_id' => '',
    ];

    protected function queryString(): array
    {
        return ['activeTab' => ['except' => 'accounts']];
    }

    public function mount()
    {
        $this->activeTab = request()->query('tab', 'accounts');
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // ===== Account CRUD =====

    public function createAccount()
    {
        $this->resetFormData();
        $this->editing = false;
        $this->editId = null;
        $this->showModal = true;
    }

    public function editAccount($id)
    {
        $coa = COA::findOrFail($id);
        $this->formData = [
            'kode_akun' => $coa->kode_akun,
            'nama_akun' => $coa->nama_akun,
            'kategori' => $coa->kategori,
            'saldo_normal' => $coa->saldo_normal,
            'level' => $coa->level,
            'header_coa_id' => (string) $coa->header_coa_id,
        ];
        $this->editing = true;
        $this->editId = $id;
        $this->showModal = true;
    }

    public function saveAccount()
    {
        $this->validate([
            'formData.kode_akun' => 'required|string|max:255|unique:coas,kode_akun,' . ($this->editId ?: 'NULL') . ',id',
            'formData.nama_akun' => 'required|string|max:255',
            'formData.kategori' => 'required|string|max:255',
            'formData.saldo_normal' => 'required|in:Debit,Kredit',
            'formData.level' => 'required|integer',
            'formData.header_coa_id' => 'required|exists:header_coas,id',
        ], [
            'formData.kode_akun.unique' => 'Kode akun sudah ada.',
            'formData.header_coa_id.exists' => 'Header tidak valid.',
        ]);

        $data = [
            'kode_akun' => $this->formData['kode_akun'],
            'nama_akun' => strtoupper($this->formData['nama_akun']),
            'kategori' => strtoupper($this->formData['kategori']),
            'saldo_normal' => $this->formData['saldo_normal'],
            'level' => $this->formData['level'],
            'header_coa_id' => $this->formData['header_coa_id'],
        ];

        if ($this->editing && $this->editId) {
            COA::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Akun berhasil diperbarui.');
        } else {
            COA::create($data);
            session()->flash('success', 'Akun berhasil ditambahkan.');
        }

        $this->showModal = false;
        $this->resetFormData();
    }

    public function confirmDeleteAccount($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteAccount()
    {
        COA::findOrFail($this->deleteId)->delete();
        session()->flash('success', 'Akun berhasil dihapus.');
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    // ===== Header CRUD =====

    public function createHeader()
    {
        $this->resetHeaderForm();
        $this->editingHeader = false;
        $this->headerEditId = null;
        $this->showHeaderModal = true;
    }

    public function editHeader($id)
    {
        $header = HeaderCOA::findOrFail($id);
        $this->headerForm = [
            'kode_header' => $header->kode_header,
            'nama_header' => $header->nama_header,
            'level' => $header->level,
            'parent_id' => (string) $header->parent_id,
        ];
        $this->editingHeader = true;
        $this->headerEditId = $id;
        $this->showHeaderModal = true;
    }

    public function saveHeader()
    {
        $this->validate([
            'headerForm.kode_header' => 'required|string|max:255|unique:header_coas,kode_header,' . ($this->headerEditId ?: 'NULL') . ',id',
            'headerForm.nama_header' => 'required|string|max:255',
            'headerForm.level' => 'required|integer',
        ]);

        $data = [
            'kode_header' => $this->headerForm['kode_header'],
            'nama_header' => $this->headerForm['nama_header'],
            'level' => $this->headerForm['level'],
            'parent_id' => $this->headerForm['parent_id'] ?: null,
        ];

        if ($this->editingHeader && $this->headerEditId) {
            HeaderCOA::findOrFail($this->headerEditId)->update($data);
            session()->flash('success', 'Header berhasil diperbarui.');
        } else {
            HeaderCOA::create($data);
            session()->flash('success', 'Header berhasil ditambahkan.');
        }

        $this->showHeaderModal = false;
        $this->resetHeaderForm();
    }

    public function confirmDeleteHeader($id)
    {
        $this->headerDeleteId = $id;
        $this->showHeaderDeleteModal = true;
    }

    public function deleteHeader()
    {
        HeaderCOA::findOrFail($this->headerDeleteId)->delete();
        session()->flash('success', 'Header berhasil dihapus.');
        $this->showHeaderDeleteModal = false;
        $this->headerDeleteId = null;
    }

    // ===== Reset helpers =====

    private function resetFormData()
    {
        $this->formData = [
            'kode_akun' => '',
            'nama_akun' => '',
            'kategori' => '',
            'saldo_normal' => 'Debit',
            'level' => 1,
            'header_coa_id' => '',
        ];
    }

    private function resetHeaderForm()
    {
        $this->headerForm = [
            'kode_header' => '',
            'nama_header' => '',
            'level' => 1,
            'parent_id' => '',
        ];
    }

    public function render()
    {
        $coas = COA::with('headerCoa')
            ->when($this->search, fn($q) => $q->where(function($q) {
                $q->where('kode_akun', 'like', '%' . $this->search . '%')
                  ->orWhere('nama_akun', 'like', '%' . $this->search . '%');
            }))
            ->paginate(20);

        $headers = HeaderCOA::withCount('coas')->with('parent')
            ->when($this->search, fn($q) => $q->where(function($q) {
                $q->where('kode_header', 'like', '%' . $this->search . '%')
                  ->orWhere('nama_header', 'like', '%' . $this->search . '%');
            }))
            ->paginate(20);

        $allHeaders = HeaderCOA::orderBy('kode_header')->get();
        $headerMappings = HeaderCOA::with('coas')->with('parent')->get();

        return view('livewire.coa-workspace', compact('coas', 'headers', 'allHeaders', 'headerMappings'));
    }
}
