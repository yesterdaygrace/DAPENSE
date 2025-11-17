<?php

namespace App\Http\Controllers\rootsuperuser;

use App\Export\rootsuperuser\NeracaSaldoSheet;
use App\Models\COA;
use App\Models\Jurnaling;
use App\Models\Periode;
use App\Models\HeaderCOA;
use App\Models\NeracaSaldo;
use App\Models\SaldoAwal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        $periodes = Periode::orderBy('tanggal_awal', 'desc')->get();;
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
                        'name' => date('F Y', strtotime($ym . '-01')), // hasil: 'November 2023'
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
        if (!$month) {
            return back()->withErrors('Bulan belum dipilih.');
        }

        $periode = Periode::findOrFail($periode_id);

        if (!$periode->is_rekap) {
            return back()->withErrors('This period has not been reconciled yet.');
        }

        $selectedMonth = Carbon::parse($month . '-01');

        $saldoAwalByCoa = SaldoAwal::where('periode_id', $periode_id)
            ->whereMonth('tanggal_saldo', $selectedMonth->month)
            ->whereYear('tanggal_saldo',  $selectedMonth->year)
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
                ->selectRaw("coa_id, SUM(debit) as total_debit, SUM(kredit) as total_kredit")
                ->groupBy('coa_id')
                ->get()
                ->keyBy('coa_id');

            $saldoAwalByCoa = collect();
            foreach ($saldoAwalPrev as $coa_id => $saldoAwal) {
                $jurnal = $jurnalPrev->get($coa_id);
                $saldo_akhir = ($saldoAwal->debit - $saldoAwal->kredit) + (($jurnal->total_debit ?? 0) - ($jurnal->total_kredit ?? 0));

                $saldoAwalByCoa->put($coa_id, (object)[
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

        $headerCoas = HeaderCoa::with(['children', 'coas'])->whereNull('parent_id')->get();

        foreach ($headerCoas as $header) {
            $this->processHeader($header, $neracaByCoa, $saldoAwalByCoa, $allCoas);
        }

        return view('rootsuperuser.neracasaldo.home', compact('periode', 'headerCoas', 'month'));
    }


    private function initializeSaldoAwal($currentPeriodeId, $previousPeriodeId)
    {
        $previousNeracaSaldo = NeracaSaldo::where('periode_id', $previousPeriodeId)->get();
        foreach ($previousNeracaSaldo as $saldo) {
            if (!$saldo->coa_id || !COA::where('id', $saldo->coa_id)->exists()) {
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
        '100' => ['min' => 10000001, 'max' => 89999999],
        '1001' => ['min' => 10010001, 'max' => 25999999],
        '10010' => ['min' => 10010001, 'max' => 12119999],
        '100100' => ['min' => 10010001, 'max' => 10810099],
        '1001000' => ['min' => 10010001, 'max' => 10019999],
        '1002000' => ['min' => 10020001, 'max' => 10029999],
        '1010000' => ['min' => 10100001, 'max' => 10109999],
        '1011000' => ['min' => 10110001, 'max' => 10119999],
        '1021000' => ['min' => 10210001, 'max' => 10219999],
        '1031000' => ['min' => 10310001, 'max' => 10319999],
        '1041000' => ['min' => 10410001, 'max' => 10419999],
        '1051000' => ['min' => 10510001, 'max' => 10519999],
        '1061000' => ['min' => 10610001, 'max' => 10619999],
        '1071000' => ['min' => 10710001, 'max' => 10719999],
        '1081000' => ['min' => 10810001, 'max' => 10819999],
        '1091000' => ['min' => 10910001, 'max' => 10919999],
        '1101000' => ['min' => 11010001, 'max' => 11019999],
        '1111000' => ['min' => 11110001, 'max' => 11119999],
        '1120000' => ['min' => 11200001, 'max' => 11209999],
        '1121000' => ['min' => 11210001, 'max' => 11219999],
        '1131000' => ['min' => 11310001, 'max' => 11319999],
        '1141000' => ['min' => 11410001, 'max' => 11419999],
        '1151000' => ['min' => 11510001, 'max' => 11519999],
        '1161000' => ['min' => 11610001, 'max' => 11619999],
        '1171000' => ['min' => 11710001, 'max' => 11719999],
        '1181000' => ['min' => 11810001, 'max' => 11819999],
        '121100'  => ['min' => 12110001, 'max' => 12129999],
        '1211000' => ['min' => 12110001, 'max' => 12119999],
        '1212000' => ['min' => 12120001, 'max' => 12129999],
        '1221000' => ['min' => 12210001, 'max' => 12219999],
        '1222000' => ['min' => 12220001, 'max' => 12229999],
        '1223000' => ['min' => 12230001, 'max' => 12239999],
        '1224000' => ['min' => 12240001, 'max' => 12249999],
        '1231000' => ['min' => 12310001, 'max' => 12319999],
        '1232000' => ['min' => 12320001, 'max' => 12329999],
        '1312000' => ['min' => 13120001, 'max' => 13129999],
        '1313000' => ['min' => 13130001, 'max' => 13139999],
        '1314000' => ['min' => 13140001, 'max' => 13149999],
        '1315000' => ['min' => 13150001, 'max' => 13159999],
        '1316000' => ['min' => 13160001, 'max' => 13169999],
        '1317000' => ['min' => 13170001, 'max' => 13179999],
        '1320000' => ['min' => 13200001, 'max' => 13209999],
        '1321000' => ['min' => 13210001, 'max' => 13219999],
        '1322000' => ['min' => 13220001, 'max' => 13229999],
        '1323000' => ['min' => 13230001, 'max' => 13239999],
        '1324000' => ['min' => 13240001, 'max' => 13249999],
        '1325000' => ['min' => 13250001, 'max' => 13259999],
        '141100' => ['min' => 14110001, 'max' => 14119999],
        '1411000' => ['min' => 14110001, 'max' => 14119999],
        '1421000' => ['min' => 14210001, 'max' => 14219999],
        '1511000' => ['min' => 15110001, 'max' => 15119999],
        '1512000' => ['min' => 15120001, 'max' => 15129999],
        '2000000' => ['min' => 20000001, 'max' => 20009999],
        '2111000' => ['min' => 21110001, 'max' => 21119999],
        '2112000' => ['min' => 21120001, 'max' => 21129999],
        '2113000' => ['min' => 21130001, 'max' => 21139999],
        '221100' => ['min' => 22111001, 'max' => 22119999],
        '2211100' => ['min' => 22111001, 'max' => 22111099],
        '2211110' => ['min' => 22111101, 'max' => 22111199],
        '2211200' => ['min' => 22112001, 'max' => 22112099],
        '2212200' => ['min' => 22122001, 'max' => 22122199],
        '2311000' => ['min' => 23110001, 'max' => 23119999],
        '2321000' => ['min' => 23210001, 'max' => 23219999],
        '2331000' => ['min' => 23310001, 'max' => 23319999],
        '2341000' => ['min' => 23410001, 'max' => 23419999],
        '2351000' => ['min' => 23510001, 'max' => 23519999],
        '2361000' => ['min' => 23610001, 'max' => 23619999],
        '2371000' => ['min' => 23710001, 'max' => 23719999],
        '2381000' => ['min' => 23810001, 'max' => 23819999],
        '2401000' => ['min' => 24010001, 'max' => 24019999],
        '2411000' => ['min' => 24110001, 'max' => 24119999],
        '2420000' => ['min' => 24200001, 'max' => 24209999],
        '2421000' => ['min' => 24210001, 'max' => 24219999],
        '2431000' => ['min' => 24310001, 'max' => 24319999],
        '2441000' => ['min' => 24410001, 'max' => 24419999],
        '2451000' => ['min' => 24510001, 'max' => 24519999],
        '2500000' => ['min' => 25000001, 'max' => 25009999],
        '511100' => ['min' => 51110001, 'max' => 89999999],
        '5111000' => ['min' => 51110001, 'max' => 51119999],
        '5112000' => ['min' => 51120001, 'max' => 51129999],
        '5120000' => ['min' => 51200001, 'max' => 51209999],
        '5121000' => ['min' => 51210001, 'max' => 51219999],
        '5131000' => ['min' => 51310001, 'max' => 51319999],
        '5140000' => ['min' => 51400001, 'max' => 51409999],
        '5141000' => ['min' => 51410001, 'max' => 51419999],
        '51510000' => ['min' => 51510001, 'max' => 51519999],
        '5161000' => ['min' => 51610001, 'max' => 51619999],
        '5171000' => ['min' => 51710001, 'max' => 51719999],
        '5181000' => ['min' => 51810001, 'max' => 51819999],
        '5191000' => ['min' => 51910001, 'max' => 51919999],
        '5211000' => ['min' => 52110001, 'max' => 52119999],
        '6101000' => ['min' => 61010001, 'max' => 61019999],
        '6111000' => ['min' => 61110001, 'max' => 61119999],
        '6121000' => ['min' => 61210001, 'max' => 61219999],
        '6131000' => ['min' => 61310001, 'max' => 61319999],
        '6141000' => ['min' => 61410001, 'max' => 61419999],
        '6161000' => ['min' => 61610001, 'max' => 61619999],
        '7011000' => ['min' => 70110001, 'max' => 70119999],
        '7021100' => ['min' => 70211001, 'max' => 70219999],
        '7031000' => ['min' => 70310001, 'max' => 70319999],
        '7041000' => ['min' => 70410001, 'max' => 70419999],
        '7051000' => ['min' => 70510001, 'max' => 70519999],
        '8121000' => ['min' => 81210001, 'max' => 81219999],
        '8122200' => ['min' => 81222001, 'max' => 81229999],
        '8123000' => ['min' => 81230001, 'max' => 81239999],
        '8124000' => ['min' => 81240001, 'max' => 81249999],
        '8125000' => ['min' => 81250001, 'max' => 81259999],
        '8131000' => ['min' => 81310001, 'max' => 81319999],
        '8211000' => ['min' => 82110001, 'max' => 82119999],
        '8221000' => ['min' => 82210001, 'max' => 82219999],
        '8222000' => ['min' => 82220001, 'max' => 82229999],
        '8231000' => ['min' => 82310001, 'max' => 82319999],
        '8999999' => ['min' => 89999901, 'max' => 89999999],
        '9000000' => ['min' => 90000001, 'max' => 90009999]
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

        $combinedKey = $header->kode_header . '|' . $header->nama_header;

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

            if (!$range) {
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
        return Excel::download(new NeracaSaldoSheet($periode_id, $month), $filename);
    }
}
