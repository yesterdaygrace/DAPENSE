<?php

namespace App\Http\Controllers\operator;

use App\Models\HeaderCOA;
use Illuminate\Http\Request;

class HeaderControllerOperator
{
    public function index()
    {
        $headerCoas = HeaderCOA::orderBy('kode_header', 'asc')->get();
        return view('operator.account.header.home', compact('headerCoas'));
    }

    public function create()
    {
        $headerCoas = HeaderCOA::all();
        return view('operator.account.header.create', compact('headerCoas'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'kode_header' => 'required|string|max:255|unique:header_coas,kode_header',
            'nama_header' => 'required|string|max:255',
            'level' => 'required|integer',
            'parent_id' => 'nullable|exists:header_coas,id',
        ], [
            'kode_header.unique' => 'Kode header sudah ada, silakan gunakan kode lain.',
        ]);

        HeaderCOA::create([
            'kode_header' => $request->kode_header,
            'nama_header' => $request->nama_header,
            'level' => $request->level,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('operator/account/header')->with('success', 'Header COA created successfully.');
    }

    public function update($id)
    {
        $headerCoa = HeaderCOA::findOrFail($id);
        $headerCoas = HeaderCOA::all();
        return view('operator.account.header.update', compact('headerCoa', 'headerCoas'));
    }

    public function updateSave(Request $request, $id)
    {
        $header_coa = HeaderCOA::findOrFail($id);
        $request->validate([
            'kode_header' => 'required|string|max:255|unique:header_coas,kode_header,' . $header_coa->id,
            'nama_header' => 'required|string|max:255',
            'level' => 'required|integer',
            'parent_id' => 'nullable|exists:header_coas,id',
        ], [
            'kode_header.unique' => 'Kode header sudah ada, silakan gunakan kode lain.',
        ]);

        $header_coa->update([
            'kode_header' => $request->kode_header,
            'nama_header' => $request->nama_header,
            'level' => $request->level,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('operator/account/header')->with('success', 'Header COA updated successfully.');
    }

    public function delete($id)
    {
        $header_coa = HeaderCOA::findOrFail($id);
        $header_coa->delete();

        return redirect()->route('operator/account/header')->with('success', 'Header COA deleted successfully.');
    }
}
