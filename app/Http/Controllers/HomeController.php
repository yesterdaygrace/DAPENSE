<?php

namespace App\Http\Controllers;

use App\Models\Jurnaling;
use App\Models\Periode;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    protected function getDashboardData()
    {
        $periodeAktif = Periode::where('is_rekap', false)->orderBy('tanggal_awal', 'desc')->first();
        $periodeSebelum = $periodeAktif
            ? Periode::where('tanggal_akhir', '<', $periodeAktif->tanggal_awal)->orderBy('tanggal_awal', 'desc')->first()
            : null;

        $currentTotal = (object) ['total_entries' => 0, 'total_debit' => 0, 'total_kredit' => 0];
        $prevTotal = (object) ['total_entries' => 0, 'total_debit' => 0, 'total_kredit' => 0];

        if ($periodeAktif) {
            $currentTotal = Jurnaling::select(
                DB::raw('COUNT(*) as total_entries'),
                DB::raw('COALESCE(SUM(debit), 0) as total_debit'),
                DB::raw('COALESCE(SUM(kredit), 0) as total_kredit')
            )->whereBetween('tanggal_jurnal', [$periodeAktif->tanggal_awal, $periodeAktif->tanggal_akhir])
                ->first();
        }

        if ($periodeSebelum) {
            $prevTotal = Jurnaling::select(
                DB::raw('COUNT(*) as total_entries'),
                DB::raw('COALESCE(SUM(debit), 0) as total_debit'),
                DB::raw('COALESCE(SUM(kredit), 0) as total_kredit')
            )->whereBetween('tanggal_jurnal', [$periodeSebelum->tanggal_awal, $periodeSebelum->tanggal_akhir])
                ->first();
        }

        $entriesTrend = ($prevTotal->total_entries > 0 && $currentTotal->total_entries != $prevTotal->total_entries)
            ? round((($currentTotal->total_entries - $prevTotal->total_entries) / $prevTotal->total_entries) * 100, 1)
            : null;

        $debitTrend = ($prevTotal->total_debit > 0 && $currentTotal->total_debit != $prevTotal->total_debit)
            ? round((($currentTotal->total_debit - $prevTotal->total_debit) / $prevTotal->total_debit) * 100, 1)
            : null;

        $kreditTrend = ($prevTotal->total_kredit > 0 && $currentTotal->total_kredit != $prevTotal->total_kredit)
            ? round((($currentTotal->total_kredit - $prevTotal->total_kredit) / $prevTotal->total_kredit) * 100, 1)
            : null;

        $stats = (object) [
            'total_entries' => $currentTotal->total_entries,
            'total_debit' => $currentTotal->total_debit,
            'total_kredit' => $currentTotal->total_kredit,
            'entries_trend' => $entriesTrend,
            'debit_trend' => $debitTrend,
            'kredit_trend' => $kreditTrend,
        ];

        $baseQuery = Jurnaling::query();
        if ($periodeAktif) {
            $baseQuery->whereBetween('tanggal_jurnal', [$periodeAktif->tanggal_awal, $periodeAktif->tanggal_akhir]);
        }

        $activities = (clone $baseQuery)->with('coa')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $monthlyRaw = (clone $baseQuery)->select(
            DB::raw("DATE_FORMAT(tanggal_jurnal, '%Y-%m') as month"),
            DB::raw('COUNT(*) as total'),
            DB::raw('COALESCE(SUM(debit), 0) as total_debit'),
            DB::raw('COALESCE(SUM(kredit), 0) as total_kredit')
        )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(6)
            ->get()
            ->reverse()
            ->values();

        $monthlySummary = collect();
        $prevDebit = null;
        foreach ($monthlyRaw as $m) {
            $trend = null;
            if ($prevDebit !== null && $prevDebit > 0) {
                $trend = round((($m->total_debit - $prevDebit) / $prevDebit) * 100, 1);
            }
            $m->trend = $trend;
            $monthlySummary->push($m);
            $prevDebit = $m->total_debit;
        }

        $periodes = Periode::orderBy('tanggal_awal', 'asc')->get();

        $favoriteModules = collect();

        return compact('periodes', 'periodeAktif', 'stats', 'activities', 'monthlySummary', 'favoriteModules');
    }

    public function index()
    {
        return view('dashboard.index', $this->getDashboardData());
    }

    public function homeRootSuperuser()
    {
        return view('dashboard.index', $this->getDashboardData());
    }

    public function homeOperator()
    {
        return view('dashboard.index', $this->getDashboardData());
    }

    public function homeBod()
    {
        return view('dashboard.index', $this->getDashboardData());
    }
}
