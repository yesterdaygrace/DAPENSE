<?php

namespace App\Http\Controllers\Base;

use App\Models\COA;
use App\Models\Jurnaling;
use App\Models\Periode;
use App\Models\SaldoAwal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class BukuBesarController
{
    protected function viewPrefix(): string
    {
        return Auth::user()->usertype;
    }

    protected function routePrefix(): string
    {
        return Auth::user()->usertype;
    }

    protected function bukuBesarExportClass(): string
    {
        $role = ucfirst($this->viewPrefix());

        return "App\\Export\\{$this->viewPrefix()}\\BukuBesar\\BukuBesarExport{$role}";
    }

    public function filterView()
    {
        $periodes = Periode::orderBy('tanggal_awal', 'desc')->get();
        $coas = COA::all();

        return view($this->viewPrefix() . '.bukubesar.filter', compact('periodes', 'coas'));
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

        return view($this->viewPrefix() . '.bukubesar.home', compact('coas', 'periodes', 'periodeId', 'availableMonths'));
    }

    public function searchCoaByFilter(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:periodes,id',
        ]);

        $periodeId = $request->input('periode_id');

        $periodes = Periode::orderBy('tanggal_awal', 'desc')
            ->get();

        $coas = COA::whereHas('jurnalings', function ($query) use ($periodeId) {
            $query->where('periode_id', $periodeId);
        })->get();

        return view($this->viewPrefix() . '.bukubesar.filter', compact('coas', 'periodes', 'periodeId'));
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

        return view($this->viewPrefix() . '.bukubesar.home', compact('coas', 'periodes', 'periodeId', 'availableMonths'));
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

        $selectedCoa = COA::findOrFail($coaId);
        $periodes = $periodes = Periode::orderBy('tanggal_awal', 'desc')
            ->get();
        $coas = COA::all();

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

        $saldoAwal += $saldoAwalTransactions;

        $transactions = Jurnaling::where('coa_id', $coaId)
            ->where('periode_id', $periodeId)
            ->whereBetween('tanggal_jurnal', [$tanggalAwal, $tanggalAkhir])
            ->orderBy('tanggal_jurnal', 'ASC')
            ->orderBy('nomor_bukti', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();

        $runningTotal = $saldoAwal;

        $entries = collect([
            (object) [
                'tanggal_jurnal' => $tanggalAwal,
                'nomor_bukti' => '-',
                'keterangan' => 'Saldo Awal',
                'debit' => 0.00,
                'kredit' => 0.00,
                'running_total' => $runningTotal,
            ],
        ]);

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

        return view($this->viewPrefix() . '.bukubesar.filter', compact(
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
            'bulan' => 'required|integer|min:1|max:12',
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
            ->whereMonth('tanggal_jurnal', $bulan)
            ->orderBy('tanggal_jurnal')
            ->get(['tanggal_jurnal', 'nomor_bukti', 'keterangan', 'debit', 'kredit']);

        $keteranganGabungan = Jurnaling::where('periode_id', $periodeId)
            ->whereMonth('tanggal_jurnal', $bulan)
            ->whereIn('nomor_bukti', $transactions->pluck('nomor_bukti')->unique())
            ->whereNotNull('keterangan')
            ->where('keterangan', '!=', '')
            ->get()
            ->groupBy('nomor_bukti')
            ->map(function ($group) {
                return $group->pluck('keterangan')->unique()->implode(', ');
            });

        $runningTotal = $initialBalance;

        $entries = collect();
        foreach ($transactions as $transaction) {
            $runningTotal += $transaction->debit - $transaction->kredit;

            $keterangan = $transaction->keterangan;
            if (! $keterangan || trim($keterangan) === '') {
                $keterangan = $keteranganGabungan[$transaction->nomor_bukti] ?? '';
            }

            $entries->push((object) [
                'tanggal_jurnal' => $transaction->tanggal_jurnal,
                'nomor_bukti' => $transaction->nomor_bukti,
                'keterangan' => $keterangan,
                'debit' => $transaction->debit,
                'kredit' => $transaction->kredit,
                'running_total' => $runningTotal,
            ]);
        }

        $coas = COA::all();
        $selectedCoa = COA::findOrFail($coaId);

        return view($this->viewPrefix() . '.bukubesar.home', compact('coas', 'entries', 'selectedCoa', 'bulan'));
    }

    public function showAll(Request $request)
    {
        $request->validate([
            'coa_id' => 'required|exists:coas,id',
            'periode_id' => 'required|exists:periodes,id',
            'bulan' => 'required|integer|min:1|max:12',
        ]);

        $coaId = $request->input('coa_id');
        $periodeId = $request->input('periode_id');
        $bulan = $request->input('bulan');
        $selectedCoa = COA::findOrFail($coaId);

        $availableMonths = Jurnaling::where('periode_id', $periodeId)
            ->selectRaw('DISTINCT MONTH(tanggal_jurnal) as bulan')
            ->orderBy('bulan', 'asc')
            ->pluck('bulan');

        $saldoAwal = SaldoAwal::where('coa_id', $coaId)
            ->where('periode_id', $periodeId)
            ->whereMonth('tanggal_saldo', $bulan)
            ->first();

        $runningTotal = $saldoAwal ? $saldoAwal->debit - $saldoAwal->kredit : 0;
        $entries = collect();

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
            if (! $keterangan || trim($keterangan) === '') {
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

        return view($this->viewPrefix() . '.bukubesar.home', compact(
            'coas',
            'entries',
            'selectedCoa',
            'periodes',
            'periodeId',
            'bulan',
            'availableMonths',
            'runningTotal',
            'keteranganGabungan'
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

        $saldoAwal = SaldoAwal::where('coa_id', $coaId)
            ->where('periode_id', $periodeId)
            ->whereMonth('tanggal_saldo', $bulan)
            ->first();

        $runningTotal = $saldoAwal ? $saldoAwal->debit - $saldoAwal->kredit : 0;
        $entries = collect();

        if ($saldoAwal) {
            $entries->push([
                'tanggal' => $saldoAwal->tanggal_saldo,
                'nomor_bukti' => 'Saldo Awal',
                'keterangan' => 'Saldo Awal',
                'debit' => $saldoAwal->debit,
                'kredit' => $saldoAwal->kredit,
                'running_total' => $runningTotal,
            ]);
        }

        $journalEntries = Jurnaling::where('coa_id', $coaId)
            ->where('periode_id', $periodeId)
            ->whereMonth('tanggal_jurnal', $bulan)
            ->orderBy('tanggal_jurnal', 'asc')
            ->orderBy('nomor_bukti', 'asc')
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
            if (! $keterangan || trim($keterangan) === '') {
                $keterangan = $keteranganGabungan[$entry->nomor_bukti] ?? '';
            }

            $entries->push([
                'tanggal' => $entry->tanggal_jurnal,
                'nomor_bukti' => $entry->nomor_bukti,
                'keterangan' => $keterangan,
                'debit' => $entry->debit,
                'kredit' => $entry->kredit,
                'running_total' => $runningTotal,
            ]);
        }

        $namaBulan = date('F', mktime(0, 0, 0, $bulan, 1));
        $periode = Periode::find($periodeId);
        $tahun = $periode && $periode->tanggal_awal
            ? Carbon::parse($periode->tanggal_awal)->format('Y')
            : date('Y');

        $namaAkun = str_replace(['/', '\\'], '-', $selectedCoa->nama_akun);

        $fileName = sprintf(
            'bukubesar_%s-%s_%s %s.xlsx',
            $selectedCoa->kode_akun,
            $namaAkun,
            $namaBulan,
            $tahun
        );

        $exportClass = $this->bukuBesarExportClass();

        return Excel::download(
            new $exportClass(
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
