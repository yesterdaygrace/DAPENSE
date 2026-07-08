<?php

namespace App\Http\Controllers\Base;

use App\Models\HeaderCOA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HeaderController
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
        $headerCoas = HeaderCOA::orderBy('kode_header', 'asc')->get();

        return view($this->viewPrefix().'.account.header.home', compact('headerCoas'));
    }

    public function create()
    {
        $headerCoas = HeaderCOA::all();

        return view($this->viewPrefix().'.account.header.create', compact('headerCoas'));
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'kode_header' => 'required|string|max:255|unique:header_coas,kode_header',
            'nama_header' => 'required|string|max:255',
            'level' => 'required|integer',
            'parent_id' => 'nullable|exists:header_coas,id',
        ], [
            'kode_header.unique' => 'Kode header sudah ada, silakan gunakan kode lain.',
        ]);
        $validated['nama_header'] = strtoupper($validated['nama_header']);

        HeaderCOA::create($validated);

        return redirect()->route($this->routePrefix().'/account/header')->with('success', 'Header COA berhasil ditambahkan.');
    }

    public function update($id)
    {
        $headerCoa = HeaderCOA::findOrFail($id);
        $headerCoas = HeaderCOA::all();

        return view($this->viewPrefix().'.account.header.update', compact('headerCoa', 'headerCoas'));
    }

    public function updateSave(Request $request, $id)
    {
        $header_coa = HeaderCOA::findOrFail($id);

        $validated = $request->validate([
            'kode_header' => 'required|string|max:255|unique:header_coas,kode_header,'.$header_coa->id,
            'nama_header' => 'required|string|max:255',
            'level' => 'required|integer',
            'parent_id' => 'nullable|exists:header_coas,id',
        ], [
            'kode_header.unique' => 'Kode header sudah ada, silakan gunakan kode lain.',
        ]);

        $validated['nama_header'] = strtoupper($validated['nama_header']);

        $header_coa->update([
            'kode_header' => $validated['kode_header'],
            'nama_header' => $validated['nama_header'],
            'level' => $validated['level'],
            'parent_id' => $validated['parent_id'],
        ]);

        return redirect()->route($this->routePrefix().'/account/header')
            ->with('success', 'Header COA berhasil diubah.');
    }

    public function delete($id)
    {
        $header_coa = HeaderCOA::findOrFail($id);
        $header_coa->delete();

        return redirect()->route($this->routePrefix().'/account/header')->with('success', 'Header COA berhasil dihapus.');
    }
}
