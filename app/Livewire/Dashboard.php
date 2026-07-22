<?php

namespace App\Livewire;

use App\Livewire\Concerns\HasRole;
use App\Models\Jurnaling;
use App\Models\Periode;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    use HasRole;

    // KPI stats
    public int $totalEntries = 0;
    public float $totalDebit = 0;
    public float $totalKredit = 0;
    public ?float $entriesTrend = null;
    public ?float $debitTrend = null;
    public ?float $kreditTrend = null;

    public $periodeAktif = null;
    public $periodes;
    public $activities;
    public $monthlySummary;
    public $favoriteModules;

    public function mount()
    {
        $this->loadDashboardData();
    }

    protected function loadDashboardData()
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

        $this->entriesTrend = ($prevTotal->total_entries > 0 && $currentTotal->total_entries != $prevTotal->total_entries)
            ? round((($currentTotal->total_entries - $prevTotal->total_entries) / $prevTotal->total_entries) * 100, 1)
            : null;

        $this->debitTrend = ($prevTotal->total_debit > 0 && $currentTotal->total_debit != $prevTotal->total_debit)
            ? round((($currentTotal->total_debit - $prevTotal->total_debit) / $prevTotal->total_debit) * 100, 1)
            : null;

        $this->kreditTrend = ($prevTotal->total_kredit > 0 && $currentTotal->total_kredit != $prevTotal->total_kredit)
            ? round((($currentTotal->total_kredit - $prevTotal->total_kredit) / $prevTotal->total_kredit) * 100, 1)
            : null;

        $this->totalEntries = $currentTotal->total_entries;
        $this->totalDebit = $currentTotal->total_debit;
        $this->totalKredit = $currentTotal->total_kredit;
        $this->periodeAktif = $periodeAktif;

        $baseQuery = Jurnaling::query();
        if ($periodeAktif) {
            $baseQuery->whereBetween('tanggal_jurnal', [$periodeAktif->tanggal_awal, $periodeAktif->tanggal_akhir]);
        }

        $this->activities = (clone $baseQuery)->with('coa')
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

        $this->monthlySummary = collect();
        $prevDebit = null;
        foreach ($monthlyRaw as $m) {
            $trend = null;
            if ($prevDebit !== null && $prevDebit > 0) {
                $trend = round((($m->total_debit - $prevDebit) / $prevDebit) * 100, 1);
            }
            $m->trend = $trend;
            $this->monthlySummary->push($m);
            $prevDebit = $m->total_debit;
        }

        $this->periodes = Periode::orderBy('tanggal_awal', 'asc')->get();
        $this->favoriteModules = collect();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
