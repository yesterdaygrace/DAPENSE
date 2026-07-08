<?php

namespace App\Http\Controllers\Base;

use App\Models\Otorisator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtorisatorController
{
    protected function viewPrefix(): string
    {
        return Auth::user()->usertype;
    }

    protected function routePrefix(): string
    {
        return Auth::user()->usertype;
    }

    public function index()
    {
        $otorisators = Otorisator::orderBy('id', 'asc')->get();

        return view($this->viewPrefix().'.otorisator.home', compact('otorisators'));
    }

    /**
     * Form tambah otorisator
     */
    public function create()
    {
        return view($this->viewPrefix().'.otorisator.create');
    }

    /**
     * Simpan otorisator baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_otorisator' => 'required|string|max:255',
            'jabatan_otorisator' => 'required|string|max:255',
        ]);

        Otorisator::create($request->only(['nama_otorisator', 'jabatan_otorisator']));

        return redirect()->route($this->routePrefix().'/otorisator/home')
            ->with('success', 'Otorisator berhasil ditambahkan.');
    }

    /**
     * Form edit otorisator
     */
    public function edit($id)
    {
        $otorisator = Otorisator::findOrFail($id);

        return view($this->viewPrefix().'.otorisator.update', compact('otorisator'));
    }

    /**
     * Update otorisator
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_otorisator' => 'required|string|max:255',
            'jabatan_otorisator' => 'required|string|max:255',
        ]);

        $otorisator = Otorisator::findOrFail($id);
        $otorisator->update($request->only(['nama_otorisator', 'jabatan_otorisator']));

        return redirect()->route($this->routePrefix().'/otorisator/home')
            ->with('success', 'Otorisator berhasil diperbarui.');
    }

    /**
     * Hapus otorisator
     */
    public function destroy($id)
    {
        $otorisator = Otorisator::findOrFail($id);
        $otorisator->delete();

        return redirect()->route($this->routePrefix().'/otorisator/home')
            ->with('success', 'Otorisator berhasil dihapus.');
    }
}
