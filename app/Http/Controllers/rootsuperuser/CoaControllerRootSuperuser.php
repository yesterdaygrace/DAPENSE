<?php

namespace App\Http\Controllers\rootsuperuser;

use App\Models\COA;
use App\Models\HeaderCOA;
use Illuminate\Http\Request;

class CoaControllerRootSuperuser
{
    public function index()
    {
        $coas = COA::orderBy('kode_akun', 'asc')->get();

        return view('rootsuperuser.account.coa.home', compact('coas'));
    }


    public function create()
    {
        $headers = HeaderCoa::all(); // Fetch all headers
        return view('rootsuperuser.account.coa.create', compact('headers'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'kode_akun' => 'required|string|max:255',
            'nama_akun' => 'required|string|max:255',
            'saldo_normal' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'level' => 'required|integer',
            'header_coa_id' => 'required|exists:header_coas,id',
        ]);

        COA::create($request->all());

        return redirect()->route('rootsuperuser/account/coa')->with('success', 'COA created successfully.');
    }

    public function update($id)
    {
        $coa = COA::findOrFail($id);
        $headers = HeaderCoa::all(); // Fetch all headers

        return view('rootsuperuser.account.coa.update', compact('coa', 'headers'));
    }

    public function updateSave(Request $request, $id)
    {
        $coa = COA::findOrFail($id);
        $request->validate([
            'kode_akun' => 'required|string|max:255',
            'nama_akun' => 'required|string|max:255',
            'saldo_normal' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'level' => 'required|integer',
            'header_coa_id' => 'required|exists:header_coas,id',
        ]);

        $coa->update([
            'kode_akun' => $request->kode_akun,
            'nama_akun' => $request->nama_akun,
            'saldo_normal' => $request->saldo_normal,
            'kategori' => $request->kategori,
            'level' => $request->level,
            'header_coa_id' => $request->header_coa_id,
        ]);

        $coa->update($request->all());

        return redirect()->route('rootsuperuser/account/coa')->with('success', 'COA created successfully.');
    }

    public function delete($id)
    {
        $coa = COA::findOrFail($id);
        $coa->delete();

        return redirect()->route('rootsuperuser/account/coa')->with('success', 'COA created successfully.');
    }
}
