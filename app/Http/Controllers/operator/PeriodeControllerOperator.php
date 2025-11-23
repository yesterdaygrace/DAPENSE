<?php

namespace App\Http\Controllers\operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Periode;
use App\Models\SaldoAwal;
use App\Models\Jurnaling;

class PeriodeControllerOperator extends Controller
{
    public function index()
    {
        $periodes = Periode::orderBy('tanggal_awal', 'desc')->get();
        return view('operator.periode.home', compact('periodes'));
    }


    public function create()
    {
        return view('operator.periode.create');
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

        return redirect()->route('operator/periodes')->with('success', 'Periode berhasil ditambahkan.');
    }

    public function update($id)
    {
        $periode = Periode::findOrFail($id);
        return view('operator.periode.update', compact('periode'));
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

        return redirect()->route('operator/periodes')->with('success', 'Periode berhasil diperbarui.');
    }

    public function delete($id)
    {
        $periode = Periode::findOrFail($id);

        $usedInSaldoAwal = SaldoAwal::where('periode_id', $periode->id)->exists();

        $usedInJurnaling = Jurnaling::where('periode_id', $periode->id)->exists();

        if ($usedInSaldoAwal || $usedInJurnaling) {
            return redirect()->route('operator/periodes')
                ->with('error', 'Periode tidak dapat dihapus karena sudah digunakan pada Saldo Awal atau Jurnaling.');
        }

        $periode->delete();

        return redirect()->route('operator/periodes')->with('success', 'Periode berhasil dihapus.');
    }
}
