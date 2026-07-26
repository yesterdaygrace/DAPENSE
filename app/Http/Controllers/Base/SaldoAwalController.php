<?php

namespace App\Http\Controllers\Base;

use App\Models\COA;
use App\Models\Periode;
use App\Models\SaldoAwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Facades\Activity;

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

        DB::transaction(function () use ($validatedData) {
            SaldoAwal::create($validatedData);
        });

        activity()
            ->causedBy(Auth::user())
            ->withProperties(['coa_id' => $validatedData['coa_id'], 'periode_id' => $validatedData['periode_id']])
            ->log('Saldo awal dibuat untuk COA ID ' . $validatedData['coa_id'] . ' periode ID ' . $validatedData['periode_id']);

        return redirect()->route($this->routePrefix() . '/saldoawal')->with('success', 'Saldo Awal berhasil dibuat.');
    }

    public function edit($id)
    {
        $saldo_awal = SaldoAwal::findOrFail($id);
        Gate::authorize('update', $saldo_awal);
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

        Gate::authorize('update', SaldoAwal::findOrFail($id));

        DB::transaction(function () use ($id, $validatedData) {
            $saldo_awal = SaldoAwal::findOrFail($id);
            $saldo_awal->update($validatedData);
        });

        activity()
            ->causedBy(Auth::user())
            ->withProperties(['saldo_awal_id' => $id])
            ->log('Saldo awal ID ' . $id . ' diperbarui');

        return redirect()->route($this->routePrefix() . '/saldoawal')->with('success', 'Saldo Awal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        Gate::authorize('delete', SaldoAwal::findOrFail($id));

        DB::transaction(function () use ($id) {
            $saldo_awal = SaldoAwal::findOrFail($id);
            $saldo_awal->delete();
        });

        activity()
            ->causedBy(Auth::user())
            ->withProperties(['saldo_awal_id' => $id])
            ->log('Saldo awal ID ' . $id . ' dihapus');

        return redirect()->route($this->routePrefix() . '/saldoawal')->with('success', 'Saldo Awal berhasil dihapus.');
    }
}
