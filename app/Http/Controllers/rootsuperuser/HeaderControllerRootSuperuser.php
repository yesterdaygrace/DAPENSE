<?php

namespace App\Http\Controllers\rootsuperuser;

use App\Models\HeaderCOA;
use Illuminate\Http\Request;

class HeaderControllerRootSuperuser
{
    public function index()
    {
        $headerCoas = HeaderCoa::orderBy('kode_header', 'asc')->get();
        return view('rootsuperuser.account.header.home', compact('headerCoas'));
    }

    public function create()
    {
        $headerCoas = HeaderCoa::all();
        return view('rootsuperuser.account.header.create', compact('headerCoas'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'kode_header' => 'required|string|max:255',
            'nama_header' => 'required|string|max:255',
            'level' => 'required|integer',
            'parent_id' => 'nullable|exists:header_coas,id',
        ]);

        HeaderCoa::create([
            'kode_header' => $request->kode_header,
            'nama_header' => $request->nama_header,
            'level' => $request->level,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('rootsuperuser/account/header')->with('success', 'Header COA created successfully.');
    }

    public function update($id)
    {
        $headerCoa = HeaderCoa::findOrFail($id);
        $headerCoas = HeaderCoa::all();
        return view('rootsuperuser.account.header.update', compact('headerCoa', 'headerCoas'));
    }

    public function updateSave(Request $request, $id)
    {
        $header_coa = HeaderCoa::findOrFail($id);
        $request->validate([
            'kode_header' => 'required|string|max:255',
            'nama_header' => 'required|string|max:255',
            'level' => 'required|integer',
            'parent_id' => 'nullable|exists:header_coas,id',
        ]);

        $header_coa->update([
            'kode_header' => $request->kode_header,
            'nama_header' => $request->nama_header,
            'level' => $request->level,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('rootsuperuser/account/header')->with('success', 'Header COA updated successfully.');
    }

    public function delete($id)
    {
        $header_coa = HeaderCoa::findOrFail($id);
        $header_coa->delete();

        return redirect()->route('rootsuperuser/account/header')->with('success', 'Header COA deleted successfully.');
    }
}
