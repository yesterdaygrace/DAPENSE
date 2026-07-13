<?php

namespace App\Http\Controllers\Base;

use App\Models\COA;
use App\Models\HeaderCOA;
use App\Models\Jurnaling;
use App\Models\NeracaSaldo;
use App\Models\Periode;
use App\Models\SaldoAwal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class NeracaSaldoController
{
    protected function viewPrefix(): string
    {
        return Auth::user()->usertype;
    }

    protected function routePrefix(): string
    {
        return Auth::user()->usertype;
    }

    protected function neracaSaldoSheetClass(): string
    {
        return "App\\Export\\{$this->viewPrefix()}\\NeracaSaldoSheet";
    }

    public function index($periode_id) {}

    public function indexrecap()
    {
        $periodes = Periode::orderBy('tanggal_awal', 'desc')->get();

        return view($this->viewPrefix().'.neracasaldo.recap', compact('periodes'));
    }

    public function showPerMonthNeraca(Request $request, $periode = null)
    {
        $periodes = Periode::orderBy('tanggal_awal', 'desc')->get();
        $selectedPeriode = $periode ?? $request->input('periode_id');

        $months = [];

        if ($selectedPeriode) {
            $periode = Periode::find($selectedPeriode);

            if ($periode) {
                $entries = NeracaSaldo::where('periode_id', $periode->id)
                    ->whereNotNull('month')
                    ->get();

                $activeMonths = $entries->map(function ($entry) {
                    $monthDate = date('Y-m', strtotime($entry->month));

                    return $monthDate;
                })->unique()->sortDesc()->values();

                foreach ($activeMonths as $ym) {
                    $months[] = [
                        'id' => $ym,
                        'name' => date('F Y', strtotime($ym.'-01')),
                    ];
                }
            }
        }

        return view($this->viewPrefix().'/neracasaldo/monthstampil', [
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

        return view($this->viewPrefix().'.neracasaldo.home', compact('periode', 'headerCoas', 'month'));
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
        '1' => ['min' => 1101, 'max' => 1304],
        '1.1' => ['min' => 1101, 'max' => 1116],
        '1.2' => ['min' => 1201, 'max' => 1210],
        '1.3' => ['min' => 1301, 'max' => 1304],
        '2' => ['min' => 2101, 'max' => 2204],
        '2.1' => ['min' => 2101, 'max' => 2113],
        '2.2' => ['min' => 2201, 'max' => 2204],
        '3' => ['min' => 3101, 'max' => 3203],
        '3.1' => ['min' => 3101, 'max' => 3104],
        '3.2' => ['min' => 3201, 'max' => 3203],
        '4' => ['min' => 4101, 'max' => 4204],
        '4.1' => ['min' => 4101, 'max' => 4108],
        '4.2' => ['min' => 4201, 'max' => 4204],
        '5' => ['min' => 5101, 'max' => 5310],
        '5.1' => ['min' => 5101, 'max' => 5114],
        '5.2' => ['min' => 5201, 'max' => 5210],
        '5.3' => ['min' => 5301, 'max' => 5310],
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

        $exportClass = $this->neracaSaldoSheetClass();

        return Excel::download(
            new $exportClass($periode_id, $month),
            $filename,
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    public function exportPdf(Request $request, $periode_id)
    {
        $month = $request->query('month');
        $filename = "LAPORAN_KEUANGAN_{$month}.pdf";

        $exportClass = $this->neracaSaldoSheetClass();

        return Excel::download(
            new $exportClass($periode_id, $month),
            $filename,
            \Maatwebsite\Excel\Excel::MPDF
        );
    }
}
