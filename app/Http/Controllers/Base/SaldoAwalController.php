<?php

namespace App\Http\Controllers\Base;

use App\Models\COA;
use App\Models\Periode;
use App\Models\SaldoAwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaldoAwalController
{
    protected function viewPrefix(): string
    {
        return Auth::user()->usertype;
    }

    protected function routePrefix(): string
    {
        return Auth::user()->usertype;
    }

    public function index(Request $request)
    {
        $query = SaldoAwal::with('coa', 'periode');

        if ($request->filled('periode_id')) {
            $query->where('periode_id', $request->periode_id);
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_saldo', $request->bulan);
        }

        $saldo_awals = $query->get();
        $periodes = Periode::orderBy('tanggal_awal', 'desc')->get();

        return view($this->viewPrefix() . '.saldoawal.home', compact('saldo_awals', 'periodes'));
    }

    public function create()
    {
        $coas = COA::orderBy('kode_akun', 'asc')->get();
        $periodes = Periode::orderBy('tanggal_awal', 'desc')
            ->get();

        return view($this->viewPrefix() . '.saldoawal.create', compact('coas', 'periodes'));
    }

    public function store(Request $request)
    {
        $rules = [
            'coa_id' => 'required|exists:coas,id',
            'tanggal_saldo' => 'required|date',
            'periode_id' => 'required|exists:periodes,id',
            'debit' => 'required|numeric',
        ];

        $validatedData = $request->validate($rules);

        $periode = Periode::find($validatedData['periode_id']);

        $tanggalSaldo = strtotime($validatedData['tanggal_saldo']);
        $tanggalAwal = strtotime($periode->tanggal_awal);
        $tanggalAkhir = strtotime($periode->tanggal_akhir);

        if ($tanggalSaldo < $tanggalAwal || $tanggalSaldo > $tanggalAkhir) {
            return redirect()->back()->with('error', 'Tanggal saldo harus berada dalam rentang periode yang dipilih (' .
                date('d-m-Y', $tanggalAwal) . ' s/d ' . date('d-m-Y', $tanggalAkhir) . ').')->withInput();
        }

        $validatedData['kredit'] = 0;

        SaldoAwal::create($validatedData);

        return redirect()->route($this->routePrefix() . '/saldoawal')->with('success', 'Saldo Awal berhasil dibuat.');
    }

    public function edit($id)
    {
        $saldo_awal = SaldoAwal::findOrFail($id);
        $coas = COA::orderBy('kode_akun', 'asc')->get();
        $periodes = Periode::orderBy('tanggal_awal', 'desc')->get();

        return view($this->viewPrefix() . '.saldoawal.edit', compact('saldo_awal', 'coas', 'periodes'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'coa_id' => 'required|exists:coas,id',
            'tanggal_saldo' => 'required|date',
            'periode_id' => 'required|exists:periodes,id',
            'debit' => 'required|numeric',
        ]);

        $periode = Periode::find($validatedData['periode_id']);

        $tanggalSaldo = strtotime($validatedData['tanggal_saldo']);
        $tanggalAwal = strtotime($periode->tanggal_awal);
        $tanggalAkhir = strtotime($periode->tanggal_akhir);

        if ($tanggalSaldo < $tanggalAwal || $tanggalSaldo > $tanggalAkhir) {
            return redirect()->back()->with('error', 'Tanggal saldo harus berada dalam rentang periode yang dipilih (' .
                date('d-m-Y', $tanggalAwal) . ' s/d ' . date('d-m-Y', $tanggalAkhir) . ').')->withInput();
        }

        $validatedData['kredit'] = 0;

        $saldo_awal = SaldoAwal::findOrFail($id);
        $saldo_awal->update($validatedData);

        return redirect()->route($this->routePrefix() . '/saldoawal')->with('success', 'Saldo Awal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $saldo_awal = SaldoAwal::findOrFail($id);
        $saldo_awal->delete();

        return redirect()->route($this->routePrefix() . '/saldoawal')->with('success', 'Saldo Awal berhasil dihapus.');
    }
}
