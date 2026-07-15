<?php

namespace App\Http\Controllers\Base;

use App\Models\COA;
use App\Models\HeaderCOA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoaController
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
        $coas = COA::orderBy('kode_akun', 'asc')->get();

        return view($this->viewPrefix() . '.account.coa.home', compact('coas'));
    }

    public function create()
    {
        $headers = HeaderCOA::where('level', 3)->orderBy('kode_header')->get();

        return view($this->viewPrefix() . '.account.coa.create', compact('headers'));
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'kode_akun' => 'required|string|max:255|unique:coas,kode_akun',
            'nama_akun' => 'required|string|max:255',
            'saldo_normal' => 'required|in:Debit,Kredit',
            'kategori' => 'required|string|max:255',
            'level' => 'required|integer',
            'header_coa_id' => 'required|exists:header_coas,id',
        ], [
            'kode_akun.unique' => 'Kode akun sudah ada, silakan gunakan kode lain.',
        ]);

        $validated['nama_akun'] = strtoupper($validated['nama_akun']);
        $validated['kategori'] = strtoupper($validated['kategori']);
        COA::create($validated);

        return redirect()->route($this->routePrefix() . '/account/coa')->with('success', 'COA berhasil ditambahkan.');
    }

    public function update($id)
    {
        $coa = COA::findOrFail($id);
        $headers = HeaderCOA::where('level', 3)->orderBy('kode_header')->get();

        return view($this->viewPrefix() . '.account.coa.update', compact('coa', 'headers'));
    }

    public function updateSave(Request $request, $id)
    {
        $coa = COA::findOrFail($id);
        $validated = $request->validate([
            'kode_akun' => 'required|string|max:255|unique:coas,kode_akun,' . $coa->id,
            'nama_akun' => 'required|string|max:255',
            'saldo_normal' => 'required|in:Debit,Kredit',
            'kategori' => 'required|string|max:255',
            'header_coa_id' => 'required|exists:header_coas,id',
        ], [
            'kode_akun.unique' => 'Kode akun sudah ada, silakan gunakan kode lain.',
        ]);

        $validated['nama_akun'] = strtoupper($validated['nama_akun']);
        $validated['kategori'] = strtoupper($validated['kategori']);

        $coa->update([
            'kode_akun' => $request->kode_akun,
            'nama_akun' => $validated['nama_akun'],
            'saldo_normal' => $request->saldo_normal,
            'kategori' => $validated['kategori'],
            'level' => $request->level,
            'header_coa_id' => $request->header_coa_id,
        ]);

        return redirect()->route($this->routePrefix() . '/account/coa')->with('success', 'COA berhasil diubah.');
    }

    public function delete($id)
    {
        $coa = COA::findOrFail($id);
        $coa->delete();

        return redirect()->route($this->routePrefix() . '/account/coa')->with('success', 'COA berhasil dihapus.');
    }
}
