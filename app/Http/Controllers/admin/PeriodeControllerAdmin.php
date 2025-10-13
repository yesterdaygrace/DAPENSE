<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Periode;

class PeriodeControllerAdmin extends Controller
{
    public function index()
    {
        $periodes = Periode::orderBy('tanggal_awal', 'desc')->get();
        return view('admin.periode.home', compact('periodes'));
    }


    public function create()
    {
        return view('admin.periode.create');
    }

    public function save(Request $request)
    {
        $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tanggal_awal' => 'required|date|regex:/^\d{4}-01-01$/',
            'tanggal_akhir' => 'required|date|regex:/^\d{4}-12-31$/',
        ]);

        $tahun = date('Y', strtotime($request->tanggal_awal));

        $exists = Periode::whereYear('tanggal_awal', $tahun)->exists();
        if ($exists) {
            return back()->with('error', "Periode untuk tahun $tahun sudah ada.")->withInput();
        }

        Periode::create([
            'nama_periode' => $request->nama_periode,
            'tanggal_awal' => $request->tanggal_awal,
            'tanggal_akhir' => $request->tanggal_akhir,
        ]);

        return redirect()->route('admin/periodes')->with('success', 'Periode berhasil ditambahkan.');
    }

    public function update($id)
    {
        $periode = Periode::findOrFail($id);
        return view('admin.periode.update', compact('periode'));
    }

    public function updateSave(Request $request, $id)
    {
        $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tanggal_awal' => 'required|date|regex:/^\d{4}-01-01$/',
            'tanggal_akhir' => 'required|date|regex:/^\d{4}-12-31$/',
        ]);

        $tahun = date('Y', strtotime($request->tanggal_awal));

        $duplikat = Periode::whereYear('tanggal_awal', $tahun)
            ->where('id', '!=', $id)
            ->exists();

        if ($duplikat) {
            return back()->with('error', "Periode untuk tahun $tahun sudah ada.")->withInput();
        }

        $periode = Periode::findOrFail($id);
        $periode->update([
            'nama_periode' => $request->nama_periode,
            'tanggal_awal' => $request->tanggal_awal,
            'tanggal_akhir' => $request->tanggal_akhir,
        ]);

        return redirect()->route('admin/periodes')->with('success', 'Periode berhasil diperbarui.');
    }

    public function delete($id)
    {
        $periode = Periode::findOrFail($id);
        $periode->delete();

        return redirect()->route('admin/periodes')->with('success', 'Periode deleted successfully.');
    }
}
