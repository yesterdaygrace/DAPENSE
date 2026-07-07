<?php

namespace App\Http\Controllers\rootsuperuser;

use App\Export\rootsuperuser\NeracaSaldoSheet;
use App\Models\COA;
use App\Models\HeaderCOA;
use App\Models\Jurnaling;
use App\Models\NeracaSaldo;
use App\Models\Periode;
use App\Models\SaldoAwal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class NeracaSaldoControllerRootSuperuser
{
    public function index($periode_id)
    {
        // $periode = Periode::findOrFail($periode_id);

        // if (!$periode->is_rekap) {
        //     return back()->withErrors('This period has not been reconciled yet.');
        // }

        // $previousPeriode = Periode::where('id', '<', $periode_id)
        //     ->where('is_rekap', true)
        //     ->latest('id')
        //     ->first();

        // if ($previousPeriode) {
        //     $this->initializeSaldoAwal($periode_id, $previousPeriode->id);
        // }

        // $jurnalByCoa = Jurnaling::where('periode_id', $periode_id)
        //     ->selectRaw('coa_id, SUM(debit) as total_debit, SUM(kredit) as total_kredit')
        //     ->groupBy('coa_id')
        //     ->with('coa')
        //     ->get();

        // $saldoAwalByCoa = SaldoAwal::where('periode_id', $periode_id)
        //     ->select('coa_id', 'debit', 'kredit')
        //     ->get()
        //     ->keyBy('coa_id');

        // $headerCoas = HeaderCoa::with(['children', 'coas'])->whereNull('parent_id')->get();

        // foreach ($headerCoas as $header) {
        //     $this->processHeader($header, $jurnalByCoa, $saldoAwalByCoa);
        // }

        // return view('rootsuperuser.neracasaldo.home', compact('periode', 'headerCoas'));
    }

    public function indexrecap()
    {
        $periodes = Periode::orderBy('tanggal_awal', 'desc')->get();

        return view('rootsuperuser.neracasaldo.recap', compact('periodes'));
    }

    public function showPerMonthNeraca(Request $request, $periode = null)
    {
        $periodes = Periode::orderBy('tanggal_awal', 'desc')->get();
        $selectedPeriode = $periode ?? $request->input('periode_id');

        $months = [];

        if ($selectedPeriode) {
            $periode = Periode::find($selectedPeriode);

            if ($periode) {
                // Ambil semua entri neraca_saldo untuk periode ini
                $entries = NeracaSaldo::where('periode_id', $periode->id)
                    ->whereNotNull('month')
                    ->get();

                // Ambil kombinasi unik bulan-tahun dari kolom month (format: YYYY-MM-DD)
                $activeMonths = $entries->map(function ($entry) {
                    $monthDate = date('Y-m', strtotime($entry->month)); // hasil: '2023-11'

                    return $monthDate;
                })->unique()->sortDesc()->values();

                foreach ($activeMonths as $ym) {
                    $months[] = [
                        'id' => $ym,
                        'name' => date('F Y', strtotime($ym.'-01')), // hasil: 'November 2023'
                    ];
                }
            }
        }

        return view('rootsuperuser/neracasaldo/monthstampil', [
            'periodes' => $periodes,
            'selectedPeriode' => $selectedPeriode,
            'months' => collect($months),
        ]);
    }

    public function indexmon(Request $request, $periode_id)
    {
        $month = $request->input('month');
        if (! $month) {
            return back()->withErrors('Bulan belum dipilih.');
        }

        $periode = Periode::findOrFail($periode_id);

        $selectedMonth = Carbon::parse($month.'-01');

        $saldoAwalByCoa = SaldoAwal::where('periode_id', $periode_id)
            ->whereMonth('tanggal_saldo', $selectedMonth->month)
            ->whereYear('tanggal_saldo', $selectedMonth->year)
            ->select('coa_id', 'debit', 'kredit')
            ->get()
            ->keyBy('coa_id');

        if ($saldoAwalByCoa->isEmpty()) {
            $previousMonth = $selectedMonth->copy()->subMonth();

            $saldoAwalPrev = SaldoAwal::where('periode_id', $periode_id)
                ->whereMonth('tanggal_saldo', $previousMonth->month)
                ->whereYear('tanggal_saldo', $previousMonth->year)
                ->get()
                ->keyBy('coa_id');

            $jurnalPrev = Jurnaling::where('periode_id', $periode_id)
                ->whereMonth('tanggal_jurnal', $previousMonth->month)
                ->whereYear('tanggal_jurnal', $previousMonth->year)
                ->selectRaw('coa_id, SUM(debit) as total_debit, SUM(kredit) as total_kredit')
                ->groupBy('coa_id')
                ->get()
                ->keyBy('coa_id');

            $saldoAwalByCoa = collect();
            foreach ($saldoAwalPrev as $coa_id => $saldoAwal) {
                $jurnal = $jurnalPrev->get($coa_id);
                $saldo_akhir = ($saldoAwal->debit - $saldoAwal->kredit) + (($jurnal->total_debit ?? 0) - ($jurnal->total_kredit ?? 0));

                $saldoAwalByCoa->put($coa_id, (object) [
                    'coa_id' => $coa_id,
                    'debit' => $saldo_akhir >= 0 ? $saldo_akhir : 0,
                    'kredit' => $saldo_akhir < 0 ? abs($saldo_akhir) : 0,
                ]);
            }
        }

        $neracaByCoa = NeracaSaldo::where('periode_id', $periode_id)
            ->whereMonth('month', $selectedMonth->month)
            ->whereYear('month', $selectedMonth->year)
            ->get()
            ->keyBy('coa_id');

        $allCoas = COA::all();

        $headerCoas = HeaderCOA::with(['children', 'coas'])->whereNull('parent_id')->get();

        foreach ($headerCoas as $header) {
            $this->processHeader($header, $neracaByCoa, $saldoAwalByCoa, $allCoas);
        }

        return view('rootsuperuser.neracasaldo.home', compact('periode', 'headerCoas', 'month'));
    }

    private function initializeSaldoAwal($currentPeriodeId, $previousPeriodeId)
    {
        $previousNeracaSaldo = NeracaSaldo::where('periode_id', $previousPeriodeId)->get();
        foreach ($previousNeracaSaldo as $saldo) {
            if (! $saldo->coa_id || ! COA::where('id', $saldo->coa_id)->exists()) {
                continue;
            }

            SaldoAwal::updateOrCreate(
                ['periode_id' => $currentPeriodeId, 'coa_id' => $saldo->coa_id],
                [
                    'debit' => max($saldo->balance, 0),
                    'kredit' => max(-$saldo->balance, 0),
                    'tanggal_saldo' => Carbon::parse($saldo->month)->startOfMonth()->toDateString(),
                ]
            );
        }
    }

    private $headerAccountRanges = [
        '1' => ['min' => 10000001, 'max' => 10999999],
        '1.1' => ['min' => 10010001, 'max' => 10019999],
        '1.2' => ['min' => 10020001, 'max' => 10029999],
        '1.3' => ['min' => 10030001, 'max' => 10039999],
        '2' => ['min' => 20000001, 'max' => 20999999],
        '2.1' => ['min' => 20010001, 'max' => 20019999],
        '2.2' => ['min' => 20020001, 'max' => 20029999],
        '3' => ['min' => 30000001, 'max' => 30999999],
        '3.1' => ['min' => 30010001, 'max' => 30019999],
        '3.2' => ['min' => 30020001, 'max' => 30029999],
        '4' => ['min' => 40000001, 'max' => 40999999],
        '4.1' => ['min' => 40010001, 'max' => 40019999],
        '4.2' => ['min' => 40020001, 'max' => 40029999],
        '5' => ['min' => 50000001, 'max' => 50999999],
        '5.1' => ['min' => 50010001, 'max' => 50019999],
        '5.2' => ['min' => 50020001, 'max' => 50029999],
        '5.3' => ['min' => 50030001, 'max' => 50039999],
    ];

    private $headerAccountRangesByIndex = [
        '1311000' => [
            0 => ['min' => 13110001, 'max' => 13179999],
            1 => ['min' => 13110001, 'max' => 13159999],
            2 => ['min' => 13110001, 'max' => 13119999],
        ],
    ];

    private function processHeader($header, $neracaByCoa, $saldoAwalByCoa, $allCoas, $index = null)
    {
        $header->total_debit = 0;
        $header->total_kredit = 0;
        $header->total_saldo_awal_debit = 0;
        $header->total_saldo_awal_kredit = 0;
        $header->total_saldo_akhir = 0;
        $header->coas = collect();

        $combinedKey = $header->kode_header.'|'.$header->nama_header;

        $range = $this->headerAccountRanges[$combinedKey]
            ?? $this->headerAccountRanges[$header->kode_header]
            ?? ($index !== null && isset($this->headerAccountRangesByIndex[$header->kode_header][$index])
                ? $this->headerAccountRangesByIndex[$header->kode_header][$index]
                : null);

        if ($range) {
            $coasInRange = $allCoas->filter(function ($coa) use ($range) {
                return $coa->kode_akun >= $range['min'] && $coa->kode_akun <= $range['max'];
            });

            foreach ($coasInRange as $coa) {
                $neraca = $neracaByCoa->get($coa->kode_akun);
                $saldoAwal = $saldoAwalByCoa->get($coa->id);

                $coa->saldo_awal_debit = $saldoAwal->debit ?? 0;
                $coa->saldo_awal_kredit = $saldoAwal->kredit ?? 0;
                $coa->total_debit = $neraca->debit ?? 0;
                $coa->total_kredit = $neraca->kredit ?? 0;

                $coa->saldo_akhir = ($coa->saldo_awal_debit - $coa->saldo_awal_kredit)
                    + ($coa->total_debit - $coa->total_kredit);

                $header->total_debit += $coa->total_debit;
                $header->total_kredit += $coa->total_kredit;
                $header->total_saldo_awal_debit += $coa->saldo_awal_debit;
                $header->total_saldo_awal_kredit += $coa->saldo_awal_kredit;
                $header->total_saldo_akhir += $coa->saldo_akhir;

                $header->coas->push($coa);
            }
        }

        foreach ($header->children as $i => $child) {
            $this->processHeader($child, $neracaByCoa, $saldoAwalByCoa, $allCoas, $i);

            if (! $range) {
                $header->total_debit += $child->total_debit;
                $header->total_kredit += $child->total_kredit;
                $header->total_saldo_awal_debit += $child->total_saldo_awal_debit;
                $header->total_saldo_awal_kredit += $child->total_saldo_awal_kredit;
                $header->total_saldo_akhir += $child->total_saldo_akhir;
            }
        }
    }

    public function exportExcel(Request $request, $periode_id)
    {
        $month = $request->query('month');
        $filename = "LAPORAN_KEUANGAN_{$month}.xlsx";

        return Excel::download(
            new NeracaSaldoSheet($periode_id, $month),
            $filename,
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    public function exportPdf(Request $request, $periode_id)
    {
        $month = $request->query('month');
        $filename = "LAPORAN_KEUANGAN_{$month}.pdf";

        return Excel::download(
            new NeracaSaldoSheet($periode_id, $month),
            $filename,
            \Maatwebsite\Excel\Excel::MPDF
        );
    }
}
