<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class UserManager extends Component
{
    use HasRole;

    public bool $showModal = false;
    public bool $editing = false;
    public $editId = null;
    public $deleteId = null;
    public bool $showDeleteModal = false;

    public array $formData = [
        'name' => '',
        'email' => '',
        'usertype' => '',
        'status' => 1,
        'password' => '',
    ];

    public function create()
    {
        $this->formData = ['name' => '', 'email' => '', 'usertype' => 'operator', 'status' => 1, 'password' => ''];
        $this->editing = false;
        $this->editId = null;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->formData = [
            'name' => $user->name,
            'email' => $user->email,
            'usertype' => $user->usertype,
            'status' => $user->status,
            'password' => '',
        ];
        $this->editing = true;
        $this->editId = $id;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'formData.name' => 'required|string|max:255',
            'formData.email' => 'required|email|max:255|unique:users,email,' . ($this->editId ?: 'NULL') . ',id',
            'formData.usertype' => 'required|in:rootsuperuser,admin,operator,bod',
            'formData.status' => 'required|boolean',
            'formData.password' => $this->editing ? 'nullable|min:8' : 'required|min:8',
        ]);

        $data = [
            'name' => $this->formData['name'],
            'email' => $this->formData['email'],
            'usertype' => $this->formData['usertype'],
            'status' => $this->formData['status'],
        ];

        if ($this->formData['password']) {
            $data['password'] = Hash::make($this->formData['password']);
        }

        if ($this->editing && $this->editId) {
            User::findOrFail($this->editId)->update($data);
            session()->flash('success', 'Pengguna berhasil diperbarui.');
        } else {
            if (!isset($data['password'])) {
                $data['password'] = Hash::make('password123');
            }
            User::create($data);
            session()->flash('success', 'Pengguna berhasil ditambahkan.');
        }

        $this->showModal = false;
    }

    public function confirmDelete($id)
    {
        if ($id === auth()->id()) {
            session()->flash('error', 'Tidak dapat menghapus akun sendiri.');
            return;
        }
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteUser()
    {
        if ($this->deleteId === auth()->id()) {
            session()->flash('error', 'Tidak dapat menghapus akun sendiri.');
            $this->showDeleteModal = false;
            return;
        }

        User::findOrFail($this->deleteId)->delete();
        session()->flash('success', 'Pengguna berhasil dihapus.');
        $this->showDeleteModal = false;
        $this->deleteId = null;
    }

    public function render()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('livewire.user-manager', compact('users'));
    }
}
