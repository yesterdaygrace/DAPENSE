<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Models\COA;
use App\Models\Jurnaling;
use App\Models\Periode;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    public function index()
    {
        $periodes = Periode::orderBy('tanggal_awal', 'desc')->get();
        $coas = COA::orderBy('kode_akun')->get();

        return view('modules.transactions.journal-entry', compact(
            'periodes', 
            'coas'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'periode_id' => 'required|exists:periodes,id',
            'transaction_type' => 'required|in:debit,kredit',
            'coa_id' => 'required|exists:coas,id',
            'tanggal_jurnal' => 'required|date',
            'nomor_bukti' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
            'debit' => 'nullable|numeric|min:0',
            'kredit' => 'nullable|numeric|min:0',
        ]);

        $validated['debit'] = $validated['debit'] ?? 0;
        $validated['kredit'] = $validated['kredit'] ?? 0;

        Jurnaling::create($validated);

        return redirect()
            ->back()
            ->with('success', 'Jurnal entri berhasil disimpan.');
    }
}
