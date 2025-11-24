<?php

namespace App\Http\Controllers\rootsuperuser;

use App\Export\rootsuperuser\BukuBesar\BukuBesarExportRootSuperuser;
use App\Models\COA;
use App\Models\Jurnaling;
use App\Models\Periode;
use App\Models\SaldoAkhir;
use App\Models\SaldoAwal;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BukuBesarControllerRootSuperuser
{

    public function filterView()
    {
        $periodes = Periode::orderBy('tanggal_awal', 'desc')->get();
        $coas = COA::all();

        return view('rootsuperuser.bukubesar.filter', compact('periodes', 'coas'));
    }

    public function searchCoaByPeriod(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periodes,id',
        ]);

        $periodeId = $request->input('periode_id');

        $periodes = Periode::orderBy('tanggal_awal', 'desc')->get();

        $coas = COA::whereHas('jurnalings', function ($query) use ($periodeId) {
            $query->where('periode_id', $periodeId);
        })->get();

        $availableMonths = Jurnaling::where('periode_id', $periodeId)
            ->selectRaw('DISTINCT MONTH(tanggal_jurnal) as bulan')
            ->orderBy('bulan', 'asc')
            ->pluck('bulan');

        return view('rootsuperuser.bukubesar.home', compact('coas', 'periodes', 'periodeId', 'availableMonths'));
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
        $coas = COA::whereHas('jurnalings', function ($query) use ($periodeId) {
            $query->where('periode_id', $periodeId);
        })->get();

        // Pass the variables to the view, including the selected periodeId
        return view('rootsuperuser.bukubesar.filter', compact('coas', 'periodes', 'periodeId'));
    }


    public function showLedgerForm(Request $request)
    {
        $periodeId = $request->input('periode_id', null);
        $periodes = Periode::orderBy('tanggal_awal', 'desc')->get();
        $coas = COA::all();

        $availableMonths = $periodeId
            ? Jurnaling::where('periode_id', $periodeId)
            ->selectRaw('DISTINCT MONTH(tanggal_jurnal) as bulan')
            ->orderBy('bulan')
            ->pluck('bulan')
            : collect();

        return view('rootsuperuser.bukubesar.home', compact('coas', 'periodes', 'periodeId', 'availableMonths'));
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
        $selectedCoa = COA::findOrFail($coaId);
        $periodes =  $periodes = Periode::orderBy('tanggal_awal', 'desc')
            ->get();
        $coas = COA::all();

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

        return view('rootsuperuser.bukubesar.filter', compact(
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

        $coas = COA::all();
        $selectedCoa = COA::findOrFail($coaId);

        return view('rootsuperuser.bukubesar.home', compact('coas', 'entries', 'selectedCoa', 'bulan'));
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
        $selectedCoa = COA::findOrFail($coaId);

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
            ->whereMonth('tanggal_jurnal', $bulan)
            ->orderBy('tanggal_jurnal', 'asc')
            ->orderBy('nomor_bukti', 'ASC')
            ->get(['tanggal_jurnal', 'nomor_bukti', 'keterangan', 'debit', 'kredit']);

        $keteranganGabungan = Jurnaling::where('periode_id', $periodeId)
            ->whereMonth('tanggal_jurnal', $bulan)
            ->whereIn('nomor_bukti', $journalEntries->pluck('nomor_bukti')->unique())
            ->whereNotNull('keterangan')
            ->where('keterangan', '!=', '')
            ->get()
            ->groupBy('nomor_bukti')
            ->map(function ($group) {
                return $group->pluck('keterangan')->unique()->implode(', ');
            });

        foreach ($journalEntries as $entry) {
            $runningTotal += $entry->debit - $entry->kredit;

            $keterangan = $entry->keterangan;
            if (!$keterangan || trim($keterangan) === '') {
                $keterangan = $keteranganGabungan[$entry->nomor_bukti] ?? '';
            }

            $entries->push((object) [
                'tanggal_jurnal' => $entry->tanggal_jurnal,
                'nomor_bukti' => $entry->nomor_bukti,
                'keterangan' => $keterangan,
                'debit' => $entry->debit,
                'kredit' => $entry->kredit,
                'running_total' => $runningTotal,
            ]);
        }

        $coas = COA::all();
        $periodes = Periode::orderBy('tanggal_awal', 'desc')->get();

        return view('rootsuperuser.bukubesar.home', compact(
            'coas',
            'entries',
            'selectedCoa',
            'periodes',
            'periodeId',
            'bulan',
            'availableMonths',
            'runningTotal',
            'keteranganGabungan' // dikirim ke Blade juga kalau mau dipakai di JS
        ))->with('action', 'show_all');
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'coa_id' => 'required|exists:coas,id',
            'periode_id' => 'required|exists:periodes,id',
            'bulan' => 'required|integer|min:1|max:12',
        ]);

        $coaId = $request->coa_id;
        $periodeId = $request->periode_id;
        $bulan = $request->bulan;

        $selectedCoa = COA::findOrFail($coaId);

        // Ambil saldo awal hanya jika tanggalnya sesuai dengan bulan yang dipilih
        $saldoAwal = SaldoAwal::where('coa_id', $coaId)
            ->where('periode_id', $periodeId)
            ->whereMonth('tanggal_saldo', $bulan)
            ->first();

        $runningTotal = $saldoAwal ? $saldoAwal->debit - $saldoAwal->kredit : 0;
        $entries = collect();

        if ($saldoAwal) {
            $entries->push([
                'tanggal'       => $saldoAwal->tanggal_saldo,
                'nomor_bukti'   => 'Saldo Awal',
                'keterangan'    => 'Saldo Awal',
                'debit'         => $saldoAwal->debit,
                'kredit'        => $saldoAwal->kredit,
                'running_total' => $runningTotal,
            ]);
        }

        // Ambil transaksi jurnal sesuai bulan yang dipilih
        $journalEntries = Jurnaling::where('coa_id', $coaId)
            ->where('periode_id', $periodeId)
            ->whereMonth('tanggal_jurnal', $bulan)
            ->orderBy('tanggal_jurnal', 'asc')
            ->orderBy('nomor_bukti', 'asc')
            ->get(['tanggal_jurnal', 'nomor_bukti', 'keterangan', 'debit', 'kredit']);

        // Buat keterangan gabungan per nomor bukti
        $keteranganGabungan = Jurnaling::where('periode_id', $periodeId)
            ->whereMonth('tanggal_jurnal', $bulan)
            ->whereIn('nomor_bukti', $journalEntries->pluck('nomor_bukti')->unique())
            ->whereNotNull('keterangan')
            ->where('keterangan', '!=', '')
            ->get()
            ->groupBy('nomor_bukti')
            ->map(function ($group) {
                return $group->pluck('keterangan')->unique()->implode(', ');
            });

        foreach ($journalEntries as $entry) {
            $runningTotal += $entry->debit - $entry->kredit;

            $keterangan = $entry->keterangan;
            if (!$keterangan || trim($keterangan) === '') {
                $keterangan = $keteranganGabungan[$entry->nomor_bukti] ?? '';
            }

            $entries->push([
                'tanggal'       => $entry->tanggal_jurnal,
                'nomor_bukti'   => $entry->nomor_bukti,
                'keterangan'    => $keterangan,
                'debit'         => $entry->debit,
                'kredit'        => $entry->kredit,
                'running_total' => $runningTotal,
            ]);
        }

        // Nama bulan dan tahun
        $namaBulan = date('F', mktime(0, 0, 0, $bulan, 1));
        $periode = Periode::find($periodeId);
        $tahun = $periode && $periode->tanggal_awal
            ? \Carbon\Carbon::parse($periode->tanggal_awal)->format('Y')
            : date('Y');

        // Format nama akun agar aman untuk filename
        $namaAkun = str_replace(['/', '\\'], '-', $selectedCoa->nama_akun);

        // Format nama file
        $fileName = sprintf(
            'bukubesar_%s-%s_%s %s.xlsx',
            $selectedCoa->kode_akun,
            $namaAkun,
            $namaBulan,
            $tahun
        );

        return Excel::download(
            new BukuBesarExportRootSuperuser(
                $entries,
                $tahun,
                $selectedCoa->kode_akun,
                $selectedCoa->nama_akun,
                $namaBulan
            ),
            $fileName
        );
    }
}
