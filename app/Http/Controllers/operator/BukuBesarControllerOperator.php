<?php

namespace App\Http\Controllers\operator;

use App\Models\COA;
use App\Models\Jurnaling;
use App\Models\Periode;
use App\Models\SaldoAkhir;
use App\Models\SaldoAwal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BukuBesarControllerOperator
{

    public function filterView()
    {
        $periodes = Periode::orderBy('tanggal_awal', 'desc')->get();
        $coas = Coa::all();

        return view('operator.bukubesar.filter', compact('periodes', 'coas'));
    }

    public function searchCoaByPeriod(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periodes,id',
        ]);

        $periodeId = $request->input('periode_id');

        $periodes = Periode::orderBy('tanggal_awal', 'desc')->get();

        $coas = Coa::whereHas('jurnalings', function ($query) use ($periodeId) {
            $query->where('periode_id', $periodeId);
        })->get();

        $availableMonths = Jurnaling::where('periode_id', $periodeId)
            ->selectRaw('DISTINCT MONTH(tanggal_jurnal) as bulan')
            ->orderBy('bulan', 'asc')
            ->pluck('bulan');

        return view('operator.bukubesar.home', compact('coas', 'periodes', 'periodeId', 'availableMonths'));
    }




    public function searchCoaByFilter(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periodes,id',
        ]);

        $periodeId = $request->input('periode_id');

        // Fetch all periods (including the one associated with Jurnaling)
        $periodes = Periode::orderBy('tanggal_awal', 'desc')
            ->get();

        // Fetch COAs that have been used in Jurnaling for the selected period
        $coas = Coa::whereHas('jurnalings', function ($query) use ($periodeId) {
            $query->where('periode_id', $periodeId);
        })->get();

        // Pass the variables to the view, including the selected periodeId
        return view('operator.bukubesar.filter', compact('coas', 'periodes', 'periodeId'));
    }

    // Show ledger for a specific COA within a selected period
    public function showLedgerForm(Request $request)
    {
        $periodeId = $request->input('periode_id', null);
        $periodes = Periode::orderBy('tanggal_awal', 'desc')->get();
        $coas = Coa::all();

        $availableMonths = $periodeId
            ? Jurnaling::where('periode_id', $periodeId)
            ->selectRaw('DISTINCT MONTH(tanggal_jurnal) as bulan')
            ->orderBy('bulan')
            ->pluck('bulan')
            : collect();

        return view('operator.bukubesar.home', compact('coas', 'periodes', 'periodeId', 'availableMonths'));
    }

    public function searchByDate(Request $request)
    {
        $request->validate([
            'coa_id' => 'required|exists:coas,id',
            'periode_id' => 'required|exists:periodes,id',
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
        ]);

        $coaId = $request->input('coa_id');
        $periodeId = $request->input('periode_id');
        $tanggalAwal = $request->input('tanggal_awal');
        $tanggalAkhir = $request->input('tanggal_akhir');

        // Fetch COA and period data
        $selectedCoa = Coa::findOrFail($coaId);
        $periodes =  $periodes = Periode::orderBy('tanggal_awal', 'desc')
            ->get();
        $coas = Coa::all();

        // Calculate Saldo Awal up to the day before 'tanggal_awal'
        $saldoAwal = SaldoAwal::where('coa_id', $coaId)
            ->where('periode_id', $periodeId)
            ->where('tanggal_saldo', '<=', $tanggalAwal)
            ->selectRaw('SUM(debit - kredit) as saldo_awal')
            ->value('saldo_awal') ?? 0;

        $saldoAwalTransactions = Jurnaling::where('coa_id', $coaId)
            ->where('periode_id', $periodeId)
            ->where('tanggal_jurnal', '<', $tanggalAwal)
            ->selectRaw('SUM(debit - kredit) as saldo_awal_trans')
            ->value('saldo_awal_trans') ?? 0;

        // Combine Saldo Awal + previous journal transactions before tanggal_awal
        $saldoAwal += $saldoAwalTransactions;

        // Fetch transactions within the date range
        $transactions = Jurnaling::where('coa_id', $coaId)
            ->where('periode_id', $periodeId)
            ->whereBetween('tanggal_jurnal', [$tanggalAwal, $tanggalAkhir])
            ->orderBy('tanggal_jurnal', 'ASC')
            ->orderBy('nomor_bukti', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        // Initialize running total
        $runningTotal = $saldoAwal;

        // Add Saldo Awal as the first entry
        $entries = collect([
            (object) [
                'tanggal_jurnal' => $tanggalAwal,
                'nomor_bukti' => '-',
                'keterangan' => 'Saldo Awal',
                'debit' => 0.00,
                'kredit' => 0.00,
                'running_total' => $runningTotal,
            ]
        ]);

        // Map transactions and calculate the running total
        foreach ($transactions as $transaction) {
            $runningTotal += ($transaction->debit - $transaction->kredit);
            $entries->push((object) [
                'tanggal_jurnal' => $transaction->tanggal_jurnal,
                'nomor_bukti' => $transaction->nomor_bukti,
                'keterangan' => $transaction->keterangan,
                'debit' => $transaction->debit,
                'kredit' => $transaction->kredit,
                'running_total' => $runningTotal,
            ]);
        }

        return view('operator.bukubesar.filter', compact(
            'saldoAwal',
            'coas',
            'selectedCoa',
            'periodes',
            'periodeId',
            'entries',
            'tanggalAwal',
            'tanggalAkhir'
        ))->with('action', 'filter_search');
    }



    public function showLedger(Request $request)
    {
        $request->validate([
            'coa_id' => 'required|exists:coas,id',
            'periode_id' => 'required|exists:periodes,id',
            'bulan' => 'required|integer|min:1|max:12', // Validasi bulan
        ]);

        $coaId = $request->input('coa_id');
        $periodeId = $request->input('periode_id');
        $bulan = $request->input('bulan');

        $initialBalance = Jurnaling::where('coa_id', $coaId)
            ->where('periode_id', '<', $periodeId)
            ->sum('debit') - Jurnaling::where('coa_id', $coaId)
            ->where('periode_id', '<', $periodeId)
            ->sum('kredit');

        $transactions = Jurnaling::where('coa_id', $coaId)
            ->where('periode_id', $periodeId)
            ->whereMonth('tanggal_jurnal', $bulan) // Filter berdasarkan bulan
            ->orderBy('tanggal_jurnal')
            ->get(['tanggal_jurnal', 'nomor_bukti', 'keterangan', 'debit', 'kredit']);

        $runningTotal = $initialBalance;

        $entries = collect();
        foreach ($transactions as $transaction) {
            $runningTotal += $transaction->debit - $transaction->kredit;

            $entries->push((object) [
                'tanggal_jurnal' => $transaction->tanggal_jurnal,
                'nomor_bukti' => $transaction->nomor_bukti,
                'keterangan' => $transaction->keterangan,
                'debit' => $transaction->debit,
                'kredit' => $transaction->kredit,
                'running_total' => $runningTotal,
            ]);
        }

        $coas = Coa::all();
        $selectedCoa = Coa::findOrFail($coaId);

        return view('operator.bukubesar.home', compact('coas', 'entries', 'selectedCoa', 'bulan'));
    }

    public function showAll(Request $request)
    {
        $request->validate([
            'coa_id' => 'required|exists:coas,id',
            'periode_id' => 'required|exists:periodes,id',
            'bulan' => 'required|integer|min:1|max:12', // Validasi bulan
        ]);

        $coaId = $request->input('coa_id');
        $periodeId = $request->input('periode_id');
        $bulan = $request->input('bulan');
        $selectedCoa = Coa::findOrFail($coaId);

        $availableMonths = Jurnaling::where('periode_id', $periodeId)
            ->selectRaw('DISTINCT MONTH(tanggal_jurnal) as bulan')
            ->orderBy('bulan', 'asc')
            ->pluck('bulan');

        // Ambil saldo awal hanya jika tanggalnya sesuai dengan bulan yang dipilih
        $saldoAwal = SaldoAwal::where('coa_id', $coaId)
            ->where('periode_id', $periodeId)
            ->whereMonth('tanggal_saldo', $bulan) // Hanya saldo awal dari bulan yang dipilih
            ->first();

        $runningTotal = $saldoAwal ? $saldoAwal->debit - $saldoAwal->kredit : 0;
        $entries = collect();

        // Pastikan saldo awal hanya muncul jika tanggalnya sesuai bulan yang dipilih
        if ($saldoAwal) {
            $entries->push((object) [
                'tanggal_jurnal' => $saldoAwal->tanggal_saldo,
                'nomor_bukti' => 'Saldo Awal',
                'keterangan' => 'Saldo Awal',
                'debit' => $saldoAwal->debit,
                'kredit' => $saldoAwal->kredit,
                'running_total' => $runningTotal,
            ]);
        }

        // Ambil transaksi jurnal sesuai bulan yang dipilih
        $journalEntries = Jurnaling::where('coa_id', $coaId)
            ->where('periode_id', $periodeId)
            ->whereMonth('tanggal_jurnal', $bulan) // Filter berdasarkan bulan
            ->orderBy('tanggal_jurnal', 'asc')
            ->orderBy('nomor_bukti', 'ASC')
            ->get(['tanggal_jurnal', 'nomor_bukti', 'keterangan', 'debit', 'kredit']);

        foreach ($journalEntries as $entry) {
            $runningTotal += $entry->debit - $entry->kredit;

            $entries->push((object) [
                'tanggal_jurnal' => $entry->tanggal_jurnal,
                'nomor_bukti' => $entry->nomor_bukti,
                'keterangan' => $entry->keterangan,
                'debit' => $entry->debit,
                'kredit' => $entry->kredit,
                'running_total' => $runningTotal,
            ]);
        }

        $coas = Coa::all();
        $periodes = Periode::orderBy('tanggal_awal', 'desc')->get();

        return view('operator.bukubesar.home', compact('coas', 'entries', 'selectedCoa', 'periodes', 'periodeId', 'bulan', 'availableMonths', 'runningTotal'))
            ->with('action', 'show_all');
    }
}
