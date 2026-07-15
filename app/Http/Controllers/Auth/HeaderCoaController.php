<?php

namespace App\Http\Controllers;

use App\Models\HeaderCoa;
use Illuminate\Http\Request;

class HeaderCoaController extends Controller
{
    public function index()
    {
        $headerCoas = HeaderCoa::paginate(10);

        return view('admin.account.header.home', compact('headerCoas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.account.header.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function save(Request $request)
    {
        $request->validate([
            'kode_header' => 'required|string|max:255',
            'nama_header' => 'required|string|max:255',
            'level' => 'required|integer',
        ]);

        HeaderCoa::create([
            'kode_header' => $request->kode_header,
            'nama_header' => $request->nama_header,
            'level' => $request->level,
        ]);

        return redirect()->route('admin/account/header')->with('success', 'Header COA created successfully.');
    }

    public function update(Request $request, HeaderCoa $header_coa)
    {
        $request->validate([
            'kode_header' => 'required|string|max:255',
            'nama_header' => 'required|string|max:255',
            'level' => 'required|integer',
        ]);

        $header_coa->update([
            'kode_header' => $request->kode_header,
            'nama_header' => $request->nama_header,
            'level' => $request->level,
        ]);

        return redirect()->route('admin/account/header/home')->with('success', 'Header COA updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HeaderCoa $header_coa)
    {
        $header_coa->delete();

        return redirect()->route('admin/account/header/home')->with('success', 'Header COA deleted successfully.');
    }
}
