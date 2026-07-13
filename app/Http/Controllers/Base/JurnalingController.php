<?php

namespace App\Http\Controllers\Base;

use App\Models\COA;
use App\Models\Jurnaling;
use App\Models\NeracaSaldo;
use App\Models\Periode;
use App\Models\SaldoAwal;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class JurnalingController
{
    protected function viewPrefix(): string
    {
        return Auth::user()->usertype;
    }

    protected function routePrefix(): string
    {
        return Auth::user()->usertype;
    }

    protected function jurnalingSheetClass(): string
    {
        return "App\\Export\\{$this->viewPrefix()}\\Jurnaling\\JurnalingSheet";
    }

    private function viewSuffix(string $type): string
    {
        return match ($type) {
            'km' => 'home',
            'kk' => 'kaskeluar',
            'bm' => 'bankmasuk',
            'bk' => 'bankkeluar',
            'mem' => 'memorial',
            'mempenutup' => 'memorialpenutup',
        };
    }

    private function routeSuffix(string $type): string
    {
        $suffix = $this->viewSuffix($type);

        return $suffix === 'home' ? 'jurnaling' : 'jurnaling/'.$suffix;
    }

    public function create()
    {
        return view($this->viewPrefix().'.jurnaling.create');
    }

    public function save(Request $request)
    {
        $periode = Periode::create($request->all());
        $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
        ]);

        return redirect()->route($this->routePrefix().'/jurnaling', ['periode_id' => $periode->id])
            ->with('successPeriode', 'Periode created successfully.');
    }

    public function index(Request $request)
    {
        return $this->renderIndex($request, 'km');
    }

    public function indexkaskeluar(Request $request)
    {
        return $this->renderIndex($request, 'kk');
    }

    public function indexbankmasuk(Request $request)
    {
        return $this->renderIndex($request, 'bm');
    }

    public function indexbankkeluar(Request $request)
    {
        return $this->renderIndex($request, 'bk');
    }

    public function indexmemorial(Request $request)
    {
        return $this->renderIndex($request, 'mem');
    }

    public function indexmemorialpenutup(Request $request)
    {
        return $this->renderIndex($request, 'mempenutup');
    }

    private function renderIndex(Request $request, string $type)
    {
        $periodeId = $request->query('periode_id', session('selectedPeriode'));
        $coas = COA::all();
        $periodes = Periode::orderBy('tanggal_awal', 'desc')->get();

        $jurnalings = [];
        if ($periodeId) {
            $jurnalings = Jurnaling::with('coa', 'periode')
                ->where('periode_id', $periodeId)
                ->get();
        }
        session(['selectedPeriode' => $periodeId]);

        return view($this->viewPrefix().'.jurnaling.'.$this->viewSuffix($type), [
            'jurnalings' => $jurnalings,
            'coas' => $coas,
            'periodes' => $periodes,
            'sortField' => $request->query('sortField', 'tanggal_jurnal'),
            'sortOrder' => $request->query('sortOrder', 'asc'),
            'selectedPeriode' => $periodeId,
        ]);
    }

    private function validateJurnaling(Request $request): array
    {
        try {
            return $request->validate([
                'tanggal_jurnal' => 'required|date',
                'nomor_bukti' => 'required|string|max:255|unique:jurnalings,nomor_bukti',
                'keterangan' => 'required|array',
                'keterangan.*' => 'nullable|string|max:255',
                'coa_id' => 'required|array',
                'coa_id.*' => 'exists:coas,id',
                'debit' => 'required|array',
                'debit.*' => 'numeric|min:0',
                'kredit' => 'required|array',
                'kredit.*' => 'numeric|min:0',
                'periode_id' => 'required|exists:periodes,id',
                'kategori_jurnal' => 'required|string|max:255',
            ]);
        } catch (ValidationException $e) {
            abort(422, json_encode(['errors' => $e->errors()]));
        }
    }

    private function validateUpdate(Request $request): void
    {
        $request->validate([
            'tanggal_jurnal' => 'required|date',
            'nomor_bukti' => 'required|string|max:255',
            'keterangan' => 'required|array',
            'keterangan.*' => 'nullable|string|max:255',
            'coa_id' => 'required|array',
            'coa_id.*' => 'exists:coas,id',
            'debit' => 'required|array',
            'kredit' => 'required|array',
            'debit.*' => 'numeric|min:0',
            'kredit.*' => 'numeric|min:0',
            'periode_id' => 'required|exists:periodes,id',
            'kategori_jurnal' => 'required|string|max:255',
        ]);
    }

    private function validateDateInPeriode(array $validated): bool
    {
        return $validated['tanggal_jurnal'] >= Carbon::parse(Periode::find($validated['periode_id'])->tanggal_awal)
            && $validated['tanggal_jurnal'] <= Carbon::parse(Periode::find($validated['periode_id'])->tanggal_akhir);
    }

    private function validateYearMatch(array $validated): bool
    {
        $periode = Periode::find($validated['periode_id']);
        $tahunPeriode = Carbon::parse($periode->tanggal_awal)->year;
        $tahunJurnal = Carbon::parse($validated['tanggal_jurnal'])->year;

        return $tahunJurnal === $tahunPeriode;
    }

    private function checkBalance(array $validated): bool
    {
        return bccomp(array_sum($validated['debit'] ?? []), array_sum($validated['kredit'] ?? []), 2) === 0;
    }

    private function buildEntry(array $validated, int $index): array
    {
        return [
            'tanggal_jurnal' => $validated['tanggal_jurnal'],
            'nomor_bukti' => $validated['nomor_bukti'],
            'keterangan' => $validated['keterangan'][$index] ?? '',
            'coa_id' => $validated['coa_id'][$index],
            'debit' => $validated['debit'][$index] ?? 0,
            'kredit' => $validated['kredit'][$index] ?? 0,
            'periode_id' => $validated['periode_id'],
            'kategori_jurnal' => $validated['kategori_jurnal'],
        ];
    }

    private function sortEntriesByDebit(array &$entries): void
    {
        $debitEntries = [];
        $kreditEntries = [];
        foreach ($entries as $entry) {
            if ($entry['debit'] > 0) {
                $debitEntries[] = $entry;
            } else {
                $kreditEntries[] = $entry;
            }
        }
        $entries = array_merge($debitEntries, $kreditEntries);
    }

    public function store(Request $request)
    {
        return $this->storeEntry($request, 'km');
    }

    public function storekaskeluar(Request $request)
    {
        return $this->storeEntry($request, 'kk');
    }

    public function storebankmasuk(Request $request)
    {
        return $this->storeEntry($request, 'bm');
    }

    public function storebankkeluar(Request $request)
    {
        return $this->storeEntry($request, 'bk');
    }

    public function storememorial(Request $request)
    {
        return $this->storeEntry($request, 'mem');
    }

    public function storememorialpenutup(Request $request)
    {
        return $this->storeEntry($request, 'mempenutup');
    }

    private function storeEntry(Request $request, string $type)
    {
        $validated = $this->validateJurnaling($request);

        if ($type !== 'km') {
            $periode = Periode::find($validated['periode_id']);
            $tahunPeriode = Carbon::parse($periode->tanggal_awal)->year;
            $tahunJurnal = Carbon::parse($validated['tanggal_jurnal'])->year;

            if ($tahunJurnal !== $tahunPeriode) {
                return response()->json(['errors' => ['Tahun jurnal ('.$tahunJurnal.') tidak sesuai dengan tahun periode ('.$tahunPeriode.').']], 422);
            }
        }

        if ($type === 'km') {
            if (! $this->validateDateInPeriode($validated)) {
                return response()->json(['errors' => ['Tanggal jurnal harus berada dalam periode yang dipilih.']], 422);
            }
        }

        if (! $this->checkBalance($validated)) {
            return response()->json(['errors' => ['Total Debit dan Kredit harus seimbang.']], 422);
        }

        $sortEntries = in_array($type, ['km', 'kk', 'bm', 'bk']);
        $entries = [];

        foreach ($validated['coa_id'] as $index => $coa_id) {
            $entries[] = $this->buildEntry($validated, $index);
        }

        if ($sortEntries) {
            $this->sortEntriesByDebit($entries);
        }

        foreach ($entries as $entry) {
            Jurnaling::create($entry);
        }

        return response()->json([
            'success' => 'Data berhasil diinputkan!',
            'redirect' => route($this->routePrefix().'/'.$this->routeSuffix($type)),
        ]);
    }

    public function cekNomorBuktiKM(Request $request)
    {
        return $this->cekNomorBukti($request, 'km');
    }

    public function cekNomorBuktiKK(Request $request)
    {
        return $this->cekNomorBukti($request, 'kk');
    }

    public function cekNomorBuktiBM(Request $request)
    {
        return $this->cekNomorBukti($request, 'bm');
    }

    public function cekNomorBuktiBK(Request $request)
    {
        return $this->cekNomorBukti($request, 'bk');
    }

    public function cekNomorBuktiMem(Request $request)
    {
        return $this->cekNomorBukti($request, 'mem');
    }

    public function cekNomorBuktiMemPenutup(Request $request)
    {
        return $this->cekNomorBukti($request, 'mempenutup');
    }

    private function cekNomorBukti(Request $request, string $type)
    {
        $nomorTransaksi = $request->get('nomor_transaksi');
        $tanggalInput = $request->get('tanggal_jurnal');

        if (! $tanggalInput) {
            return response()->json(['error' => 'Tanggal jurnal tidak ditemukan.'], 400);
        }

        $tanggal = DateTime::createFromFormat('Y-m-d', $tanggalInput);
        if (! $tanggal) {
            return response()->json(['error' => 'Format tanggal tidak valid.'], 400);
        }

        $bulan = str_pad($tanggal->format('n'), 2, '0', STR_PAD_LEFT);
        $tahun = substr($tanggal->format('Y'), -2);

        $nomorBukti = match ($type) {
            'km' => 'KM-'.str_pad($nomorTransaksi, 4, '0', STR_PAD_LEFT).'/'.$bulan.'/'.$tahun,
            'kk' => 'KK-'.str_pad($nomorTransaksi, 4, '0', STR_PAD_LEFT).'/'.$bulan.'/'.$tahun,
            'bm' => substr($request->get('nomor_akun'), -4).'-BM-'.str_pad($nomorTransaksi, 4, '0', STR_PAD_LEFT).'/'.$bulan.'/'.$tahun,
            'bk' => substr($request->get('nomor_akun'), -4).'-BK-'.str_pad($nomorTransaksi, 4, '0', STR_PAD_LEFT).'/'.$bulan.'/'.$tahun,
            'mem', 'mempenutup' => 'JM-'.str_pad($nomorTransaksi, 4, '0', STR_PAD_LEFT).'/'.$bulan.'/'.$tahun,
        };

        $jurnal = Jurnaling::where('nomor_bukti', $nomorBukti)->get();

        if ($jurnal->count() === 0) {
            return response()->json(['exists' => false]);
        }

        $jurnalDebit = $jurnal->where('debit', '>', 0)->first();
        $jurnalKredit = $jurnal->where('kredit', '>', 0)->first();

        $additionalCoas = [];
        $includeBoth = in_array($type, ['mem', 'mempenutup']);
        $includeKreditInAdd = ! in_array($type, ['kk', 'bk', 'mem', 'mempenutup']);
        $includeDebitInAdd = in_array($type, ['kk', 'bk', 'mem', 'mempenutup']);

        $jurnal->where('id', '!=', $jurnalDebit->id)->where('id', '!=', $jurnalKredit->id)->each(function ($item) use (&$additionalCoas, $includeBoth, $includeKreditInAdd, $includeDebitInAdd) {
            $add = [
                'id' => $item->id,
                'coa_id' => $item->coa_id,
                'kode_akun' => $item->coa ? $item->coa->kode_akun : '',
                'nama_akun' => $item->coa ? $item->coa->nama_akun : '',
                'keterangan' => $item->keterangan,
            ];
            if ($includeBoth || $includeDebitInAdd) {
                $add['debit'] = $item->debit;
            }
            if ($includeBoth || $includeKreditInAdd) {
                $add['kredit'] = $item->kredit;
            }
            $additionalCoas[] = $add;
        });

        $response = [
            'exists' => true,
            'id_debit' => $jurnalDebit->id,
            'tanggal_jurnal' => $jurnalDebit->tanggal_jurnal,
            'kategori_jurnal' => $jurnalDebit->kategori_jurnal,
            'keterangan_debit' => $jurnalDebit->keterangan,
            'coa1_id' => $jurnalDebit->coa_id,
            'coa1_kode' => $jurnalDebit->coa ? $jurnalDebit->coa->kode_akun : '',
            'coa1_nama' => $jurnalDebit->coa ? $jurnalDebit->coa->nama_akun : '',
            'debit' => $jurnalDebit->debit,
            'id_kredit' => $jurnalKredit->id,
            'coa2_id' => $jurnalKredit->coa_id,
            'coa2_kode' => $jurnalKredit->coa ? $jurnalKredit->coa->kode_akun : '',
            'coa2_nama' => $jurnalKredit->coa ? $jurnalKredit->coa->nama_akun : '',
            'kredit' => $jurnalKredit->kredit,
            'additional_coas' => $additionalCoas,
        ];

        $includeKeteranganKredit = in_array($type, ['km', 'bm', 'mem', 'mempenutup']);
        if ($includeKeteranganKredit) {
            $response['keterangan_kredit'] = $jurnalKredit->keterangan;
        }

        return response()->json($response);
    }

    public function updatekm(Request $request, $id)
    {
        return $this->updateEntry($request, 'km', $id);
    }

    public function updatekk(Request $request, $id)
    {
        return $this->updateEntry($request, 'kk', $id);
    }

    public function updatebm(Request $request, $id)
    {
        return $this->updateEntry($request, 'bm', $id);
    }

    public function updatebk(Request $request, $id)
    {
        return $this->updateEntry($request, 'bk', $id);
    }

    public function updatemem(Request $request, $id)
    {
        return $this->updateEntry($request, 'mem', $id);
    }

    public function updatemempenutup(Request $request, $id)
    {
        return $this->updateEntry($request, 'mempenutup', $id);
    }

    private function updateEntry(Request $request, string $type, $id)
    {
        $this->validateUpdate($request);

        $debitSum = array_sum($request->debit);
        $kreditSum = array_sum($request->kredit);

        $useBccomp = in_array($type, ['km', 'bm']);
        $balanceCheck = $useBccomp
            ? bccomp($debitSum, $kreditSum, 2) === 0
            : $debitSum === $kreditSum;

        if (! $balanceCheck) {
            return back()->withErrors(['message' => 'Total Debit dan Kredit harus seimbang.']);
        }

        $nomorBukti = trim($request->nomor_bukti);
        $entries = Jurnaling::where('nomor_bukti', $nomorBukti)->get();

        $isMemType = in_array($type, ['mem', 'mempenutup']);
        if ($isMemType && $entries->isEmpty()) {
            return back()->withErrors(['message' => "Nomor bukti {$nomorBukti} tidak ditemukan di database."]);
        }

        $sortEntries = in_array($type, ['km', 'bm']);
        $mergeMode = in_array($type, ['km', 'bm']);

        if ($mergeMode) {
            $existingEntries = $entries->values();
            $finalEntries = [];

            foreach ($request->coa_id as $index => $coa_id) {
                $finalEntries[] = [
                    'tanggal_jurnal' => $request->tanggal_jurnal,
                    'nomor_bukti' => $nomorBukti,
                    'keterangan' => $request->keterangan[$index] ?? '',
                    'coa_id' => $coa_id,
                    'debit' => is_numeric($request->debit[$index]) ? (float) str_replace(',', '', $request->debit[$index]) : 0,
                    'kredit' => is_numeric($request->kredit[$index]) ? (float) str_replace(',', '', $request->kredit[$index]) : 0,
                    'periode_id' => $request->periode_id,
                    'kategori_jurnal' => $request->kategori_jurnal,
                ];
            }

            if ($sortEntries) {
                $this->sortEntriesByDebit($finalEntries);
            }

            foreach ($finalEntries as $i => $entry) {
                if (isset($existingEntries[$i])) {
                    $existingEntries[$i]->update($entry);
                } else {
                    Jurnaling::create($entry);
                }
            }

            if ($existingEntries->count() > count($finalEntries)) {
                foreach ($existingEntries->slice(count($finalEntries)) as $entry) {
                    $entry->delete();
                }
            }
        } else {
            foreach ($entries as $index => $entry) {
                $entry->update([
                    'tanggal_jurnal' => $request->tanggal_jurnal,
                    'nomor_bukti' => $nomorBukti,
                    'keterangan' => $request->keterangan[$index] ?? $entry->keterangan,
                    'coa_id' => $request->coa_id[$index] ?? $entry->coa_id,
                    'debit' => is_numeric($request->debit[$index]) ? (float) str_replace(',', '', $request->debit[$index]) : 0,
                    'kredit' => is_numeric($request->kredit[$index]) ? (float) str_replace(',', '', $request->kredit[$index]) : 0,
                    'periode_id' => $request->periode_id,
                    'kategori_jurnal' => $request->kategori_jurnal,
                ]);
            }
        }

        return redirect()->route($this->routePrefix().'/'.$this->routeSuffix($type))
            ->with([
                'selectedPeriode' => $request->periode_id,
                'success' => 'Data berhasil diperbarui!',
            ]);
    }

    public function deletekm(Request $request)
    {
        return $this->deleteEntry($request, 'km');
    }

    public function deletekk(Request $request)
    {
        return $this->deleteEntry($request, 'kk');
    }

    public function deletebk(Request $request)
    {
        return $this->deleteEntry($request, 'bk');
    }

    public function deletebm(Request $request)
    {
        return $this->deleteEntry($request, 'bm');
    }

    public function deletemem(Request $request)
    {
        return $this->deleteEntry($request, 'mem');
    }

    public function deletemempenutup(Request $request)
    {
        return $this->deleteEntry($request, 'mempenutup');
    }

    private function deleteEntry(Request $request, string $type)
    {
        try {
            $nomorBukti = $request->input('nomor_bukti');
            $jurnal = Jurnaling::where('nomor_bukti', $nomorBukti)->first();

            if (! $jurnal) {
                return redirect()->route($this->routePrefix().'/'.$this->routeSuffix($type))
                    ->with([
                        'selectedPeriode' => $request->periode_id,
                        'error' => 'Jurnal dengan nomor bukti tersebut tidak ditemukan.',
                    ]);
            }

            Jurnaling::where('nomor_bukti', $nomorBukti)->delete();

            return redirect()->route($this->routePrefix().'/'.$this->routeSuffix($type))
                ->with([
                    'selectedPeriode' => $request->periode_id,
                    'success' => 'Data berhasil dihapus!',
                ]);
        } catch (\Exception $e) {
            return redirect()->route($this->routePrefix().'/'.$this->routeSuffix($type))
                ->with([
                    'selectedPeriode' => $request->periode_id,
                    'error' => 'Terjadi kesalahan saat menghapus jurnal: '.$e->getMessage(),
                ]);
        }
    }

    public function showPerMonth(Request $request, $periode = null)
    {
        $periodes = Periode::orderBy('tanggal_awal', 'desc')->get();
        $selectedPeriode = $periode ?? $request->input('periode_id');

        $months = [];
        if ($selectedPeriode) {
            $periode = Periode::find($selectedPeriode);
            if ($periode) {
                $activeMonths = Jurnaling::where('periode_id', $periode->id)
                    ->selectRaw('DATE_FORMAT(tanggal_jurnal, "%Y-%m") as ym')
                    ->groupBy('ym')
                    ->pluck('ym')
                    ->toArray();

                $startDate = strtotime($periode->tanggal_awal);
                $endDate = strtotime($periode->tanggal_akhir);

                while ($startDate <= $endDate) {
                    $ym = date('Y-m', $startDate);
                    if (in_array($ym, $activeMonths)) {
                        $months[] = [
                            'id' => $ym,
                            'name' => date('F Y', $startDate),
                        ];
                    }
                    $startDate = strtotime('+1 month', $startDate);
                }
                $months = array_reverse($months);
            }
        }

        return view($this->viewPrefix().'/neracasaldo/months', [
            'periodes' => $periodes,
            'selectedPeriode' => $selectedPeriode,
            'months' => collect($months),
        ]);
    }

    public function showMonths(Request $request, $periode = null)
    {
        $periodes = Periode::orderBy('tanggal_awal', 'desc')->get();
        $selectedPeriode = $periode ?? $request->input('periode_id');

        $months = [];
        if ($selectedPeriode) {
            $periode = Periode::find($selectedPeriode);
            if ($periode) {
                $activeMonths = Jurnaling::where('periode_id', $periode->id)
                    ->selectRaw('DATE_FORMAT(tanggal_jurnal, "%Y-%m") as ym')
                    ->groupBy('ym')
                    ->pluck('ym')
                    ->toArray();

                $startDate = strtotime($periode->tanggal_awal);
                $endDate = strtotime($periode->tanggal_akhir);

                while ($startDate <= $endDate) {
                    $ym = date('Y-m', $startDate);
                    if (in_array($ym, $activeMonths)) {
                        $months[] = [
                            'id' => $ym,
                            'name' => date('F Y', $startDate),
                        ];
                    }
                    $startDate = strtotime('+1 month', $startDate);
                }
                $months = array_reverse($months);
            }
        }

        return view($this->viewPrefix().'/jurnaling/months', compact('periodes', 'selectedPeriode', 'months'));
    }

    public function rekapJurnalMonth(Request $request, $periode_id)
    {
        $periode = Periode::findOrFail($periode_id);
        $selectedMonthDate = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();

        $journalEntries = Jurnaling::where('periode_id', $periode_id)
            ->whereMonth('tanggal_jurnal', $selectedMonthDate->month)
            ->get();

        $balanceByCoa = $journalEntries->groupBy('coa_id')->map(function ($group) {
            return [
                'debit' => $group->sum('debit'),
                'kredit' => $group->sum('kredit'),
            ];
        });

        $allSaldoAwal = SaldoAwal::where('periode_id', $periode_id)
            ->whereDate('tanggal_saldo', $selectedMonthDate->toDateString())
            ->get()
            ->keyBy('coa_id');

        $nextPeriode = null;
        if ($selectedMonthDate->month === 12) {
            $nextYear = $selectedMonthDate->year + 1;
            $nextPeriode = Periode::firstOrCreate([
                'tanggal_awal' => Carbon::create($nextYear, 1, 1)->toDateString(),
                'tanggal_akhir' => Carbon::create($nextYear, 12, 31)->toDateString(),
            ], [
                'nama_periode' => $nextYear,
                'is_rekap' => false,
            ]);
        }

        $neracaSaldoBatch = [];
        $saldoAwalBatch = [];

        COA::chunk(50, function ($coas) use (
            $periode_id, $selectedMonthDate, $balanceByCoa, $nextPeriode, $allSaldoAwal,
            &$neracaSaldoBatch, &$saldoAwalBatch
        ) {
            foreach ($coas as $coa) {
                if (in_array($coa->kode_akun, ['3202', '3201'])) {
                    continue;
                }

                $existingSaldoAwal = $allSaldoAwal->get($coa->id);
                $saldoAwalDebit = $existingSaldoAwal?->debit ?? 0;
                $saldoAwalKredit = $existingSaldoAwal?->kredit ?? 0;

                $debit = $balanceByCoa->get($coa->id)['debit'] ?? 0;
                $kredit = $balanceByCoa->get($coa->id)['kredit'] ?? 0;
                $saldoAkhir = ($saldoAwalDebit - $saldoAwalKredit) + ($debit - $kredit);

                $neracaSaldoBatch[] = [
                    'coa_id' => (string) $coa->kode_akun,
                    'periode_id' => $periode_id,
                    'month' => $selectedMonthDate->toDateString(),
                    'debit' => $debit,
                    'kredit' => $kredit,
                    'balance' => $saldoAkhir,
                    'saldo_awal' => $saldoAwalDebit,
                ];

                $nextMonth = $selectedMonthDate->copy()->addMonth()->startOfMonth()->toDateString();
                $isLastMonth = $selectedMonthDate->month < 12;

                $saldoAwalBatch[] = [
                    'coa_id' => $coa->id,
                    'periode_id' => $isLastMonth ? $periode_id : $nextPeriode->id,
                    'tanggal_saldo' => $isLastMonth ? $nextMonth : Carbon::create($selectedMonthDate->year + 1, 1, 1)->toDateString(),
                    'debit' => $saldoAkhir,
                    'kredit' => 0,
                ];
            }
        });

        $totalDebit = collect($neracaSaldoBatch)->whereBetween('coa_id', ['4101', '5310'])->sum('debit');
        $totalKredit = collect($neracaSaldoBatch)->whereBetween('coa_id', ['4101', '5310'])->sum('kredit');
        $selisih = abs($totalDebit - $totalKredit);
        $isDebitGreater = $totalDebit > $totalKredit;

        $coa3202 = COA::where('kode_akun', '3202')->first();
        $coa3201 = COA::where('kode_akun', '3201')->first();

        if ($coa3202 && $coa3201) {
            $saldoAwal3202 = $allSaldoAwal->get($coa3202->id);
            $saldoAwal3201 = $allSaldoAwal->get($coa3201->id);

            $debit3202 = $balanceByCoa->get($coa3202->id)['debit'] ?? 0;
            $kredit3202 = $balanceByCoa->get($coa3202->id)['kredit'] ?? 0;

            if ($isDebitGreater) {
                $debit3202 += $selisih;
            } else {
                $kredit3202 += $selisih;
            }

            $saldoAkhir3202 = ($saldoAwal3202?->debit ?? 0 - $saldoAwal3202?->kredit ?? 0) + ($debit3202 - $kredit3202);

            $neracaSaldoBatch[] = [
                'coa_id' => '3202',
                'periode_id' => $periode_id,
                'month' => $selectedMonthDate->toDateString(),
                'debit' => $debit3202,
                'kredit' => $kredit3202,
                'balance' => $saldoAkhir3202,
                'saldo_awal' => $saldoAwal3202?->debit ?? 0,
            ];

            $isLastMonth = $selectedMonthDate->month < 12;
            $saldoAwalBatch[] = [
                'coa_id' => $coa3202->id,
                'periode_id' => $isLastMonth ? $periode_id : $nextPeriode->id,
                'tanggal_saldo' => $isLastMonth ? $selectedMonthDate->copy()->addMonth()->startOfMonth()->toDateString() : Carbon::create($selectedMonthDate->year + 1, 1, 1)->toDateString(),
                'debit' => $saldoAkhir3202,
                'kredit' => 0,
            ];

            $debit3201 = ! $isDebitGreater ? $selisih : 0;
            $kredit3201 = $isDebitGreater ? $selisih : 0;
            $saldoAkhir3201 = ($saldoAwal3201?->debit ?? 0 - $saldoAwal3201?->kredit ?? 0) + ($debit3201 - $kredit3201);

            $neracaSaldoBatch[] = [
                'coa_id' => '3201',
                'periode_id' => $periode_id,
                'month' => $selectedMonthDate->toDateString(),
                'debit' => $debit3201,
                'kredit' => $kredit3201,
                'balance' => $saldoAkhir3201,
                'saldo_awal' => $saldoAwal3201?->debit ?? 0,
            ];

            $saldoAwalBatch[] = [
                'coa_id' => $coa3201->id,
                'periode_id' => $isLastMonth ? $periode_id : $nextPeriode->id,
                'tanggal_saldo' => $isLastMonth ? $selectedMonthDate->copy()->addMonth()->startOfMonth()->toDateString() : Carbon::create($selectedMonthDate->year + 1, 1, 1)->toDateString(),
                'debit' => $saldoAkhir3201,
                'kredit' => 0,
            ];
        }

        $existingNeraca = NeracaSaldo::where('periode_id', $periode_id)
            ->where('month', $selectedMonthDate->toDateString())
            ->get()
            ->keyBy(fn ($item) => $item->coa_id.'-'.(int) $item->periode_id.'-'.Carbon::parse($item->month)->toDateString());

        $existingSaldoAwal = SaldoAwal::whereIn('tanggal_saldo', [
            $selectedMonthDate->copy()->addMonth()->startOfMonth()->toDateString(),
            Carbon::create($selectedMonthDate->year + 1, 1, 1)->toDateString(),
        ])
            ->whereIn('periode_id', [$periode_id, optional($nextPeriode)->id])
            ->get()
            ->keyBy(fn ($item) => $item->coa_id.'-'.(int) $item->periode_id.'-'.Carbon::parse($item->tanggal_saldo)->toDateString());

        $neracaSaldoBatch = collect($neracaSaldoBatch)->filter(function ($item) use ($existingNeraca) {
            $key = $item['coa_id'].'-'.(int) $item['periode_id'].'-'.Carbon::parse($item['month'])->toDateString();
            $existing = $existingNeraca->get($key);

            return ! $existing || (
                $existing->debit != $item['debit'] ||
                $existing->kredit != $item['kredit'] ||
                $existing->balance != $item['balance'] ||
                $existing->saldo_awal != $item['saldo_awal']
            );
        })->values()->all();

        $saldoAwalBatch = collect($saldoAwalBatch)->filter(function ($item) use ($existingSaldoAwal) {
            $key = $item['coa_id'].'-'.(int) $item['periode_id'].'-'.Carbon::parse($item['tanggal_saldo'])->toDateString();
            $existing = $existingSaldoAwal->get($key);

            return ! $existing || (
                $existing->debit != $item['debit'] ||
                $existing->kredit != $item['kredit']
            );
        })->values()->all();

        $seenSaldoAwal = [];
        $finalSaldoAwalBatch = [];
        foreach ($saldoAwalBatch as $row) {
            $key = $row['coa_id'].'-'.(int) $row['periode_id'].'-'.Carbon::parse($row['tanggal_saldo'])->toDateString();
            if (! isset($seenSaldoAwal[$key])) {
                $seenSaldoAwal[$key] = true;
                $finalSaldoAwalBatch[] = $row;
            }
        }

        $seenNeraca = [];
        $finalNeracaSaldoBatch = [];
        foreach ($neracaSaldoBatch as $row) {
            $key = $row['coa_id'].'-'.(int) $row['periode_id'].'-'.Carbon::parse($row['month'])->toDateString();
            if (! isset($seenNeraca[$key])) {
                $seenNeraca[$key] = true;
                $finalNeracaSaldoBatch[] = $row;
            }
        }

        NeracaSaldo::upsert(
            $finalNeracaSaldoBatch,
            ['coa_id', 'periode_id', 'month'],
            ['debit', 'kredit', 'balance', 'saldo_awal']
        );

        collect($finalSaldoAwalBatch)->chunk(50)->each(function ($chunk) {
            foreach ($chunk as $row) {
                SaldoAwal::updateOrCreate(
                    [
                        'coa_id' => $row['coa_id'],
                        'periode_id' => (int) $row['periode_id'],
                        'tanggal_saldo' => Carbon::parse($row['tanggal_saldo'])->toDateString(),
                    ],
                    [
                        'debit' => $row['debit'],
                        'kredit' => $row['kredit'],
                    ]
                );
            }
        });

        return redirect()->route($this->routePrefix().'/neracasaldo/showing', [
            'periode_id' => $periode_id,
            'month' => $selectedMonthDate->format('Y-m'),
        ])->with('success', 'Neraca Saldo berhasil direkap untuk bulan ini.');
    }

    public function rekapJurnal(Request $request, $periode_id)
    {
        $periode = Periode::findOrFail($periode_id);

        if ($periode->is_rekap) {
            return redirect()->back()->with('error', 'Jurnal sudah direkap untuk periode ini.');
        }

        $journalEntries = Jurnaling::where('periode_id', $periode_id)->get();
        $balanceByCoa = $journalEntries->groupBy('coa_id')->map(function ($group) {
            return [
                'debit' => $group->sum('debit'),
                'kredit' => $group->sum('kredit'),
            ];
        });

        $coas = COA::all();
        foreach ($coas as $coa) {
            $existingSaldoAwal = SaldoAwal::where('coa_id', $coa->id)
                ->where('periode_id', $periode_id)
                ->first();

            $saldoAwalDebit = $existingSaldoAwal ? $existingSaldoAwal->debit : 0;
            $saldoAwalKredit = $existingSaldoAwal ? $existingSaldoAwal->kredit : 0;

            $debit = $balanceByCoa->has($coa->id) ? $balanceByCoa[$coa->id]['debit'] : 0;
            $kredit = $balanceByCoa->has($coa->id) ? $balanceByCoa[$coa->id]['kredit'] : 0;
            $saldoAkhir = ($saldoAwalDebit - $saldoAwalKredit) + ($debit - $kredit);

            NeracaSaldo::updateOrCreate(
                [
                    'coa_id' => (string) $coa->kode_akun,
                    'periode_id' => $periode_id,
                ],
                [
                    'debit' => $debit,
                    'kredit' => $kredit,
                    'balance' => $saldoAkhir,
                    'saldo_awal' => $saldoAwalDebit,
                ]
            );

            $nextPeriode = Periode::where('id', '>', $periode_id)->orderBy('id', 'asc')->first();
            if ($nextPeriode) {
                SaldoAwal::updateOrCreate(
                    [
                        'coa_id' => $coa->id,
                        'periode_id' => $nextPeriode->id,
                    ],
                    [
                        'tanggal_saldo' => now(),
                        'debit' => $saldoAkhir > 0 ? $saldoAkhir : 0,
                        'kredit' => $saldoAkhir < 0 ? abs($saldoAkhir) : 0,
                    ]
                );
            }
        }

        $periode->is_rekap = true;
        $periode->save();

        return redirect()->route($this->routePrefix().'/neracasaldo', ['periode_id' => $periode_id])
            ->with('success', 'Jurnal berhasil direkap.');
    }

    public function unrekapJurnal($periode_id)
    {
        $periode = Periode::findOrFail($periode_id);
        $periode->is_rekap = false;
        $periode->save();

        return redirect()->back()->with('success', 'Period has been unrekapped successfully.');
    }

    public function showJurnaling(Request $request)
    {
        $periodes = Periode::all();
        $coas = COA::all();
        $selectedPeriode = $request->query('periode_id', null);

        $jurnalings = Jurnaling::with('coa')
            ->when($selectedPeriode, function ($query, $selectedPeriode) {
                return $query->where('periode_id', $selectedPeriode);
            })
            ->orderBy('nomor_bukti', 'ASC')
            ->orderBy('tanggal_jurnal', 'asc')
            ->get();

        return view($this->viewPrefix().'.jurnaling.showing', [
            'periodes' => $periodes,
            'coas' => $coas,
            'jurnalings' => $jurnalings,
            'selectedPeriode' => $selectedPeriode,
        ]);
    }

    public function showEntries(Request $request)
    {
        $month = $request->query('month');
        $periodeId = $request->query('periode_id');

        if (! $month || ! $periodeId) {
            return redirect()->route($this->routePrefix().'/jurnaling/months')
                ->withErrors(['error' => 'Please select a valid month and period.']);
        }

        $selectedPeriode = Periode::find($periodeId);
        if (! $selectedPeriode) {
            return redirect()->route($this->routePrefix().'/jurnaling/months')
                ->withErrors(['error' => 'Invalid period selected.']);
        }

        $year = substr($month, 0, 4);
        $monthNumber = substr($month, 5, 2);

        $monthEntries = Jurnaling::with('coa')
            ->whereYear('tanggal_jurnal', $year)
            ->whereMonth('tanggal_jurnal', $monthNumber)
            ->where('periode_id', $periodeId)
            ->orderBy('tanggal_jurnal', 'asc')
            ->orderBy('nomor_bukti', 'ASC')
            ->get()
            ->map(function ($entry) {
                if (empty(trim($entry->keterangan))) {
                    switch (strtolower($entry->kategori_jurnal)) {
                        case 'kas masuk': $entry->keterangan = 'Pemasukan Kas';
                            break;
                        case 'kas keluar': $entry->keterangan = 'Pengeluaran Kas';
                            break;
                        case 'bank masuk': $entry->keterangan = 'Pemasukan Bank';
                            break;
                        case 'bank keluar': $entry->keterangan = 'Pengeluaran Bank';
                            break;
                        default: $entry->keterangan = '-';
                    }
                }

                return $entry;
            });

        $monthName = Carbon::createFromFormat('Y-m', $month)->format('F Y');

        return view($this->viewPrefix().'.jurnaling.showing', compact('monthEntries', 'monthName'));
    }

    public function exportJurnaling(Request $request)
    {
        $month = $request->query('month');
        $periodeId = $request->query('periode_id');

        $periode = Periode::find($periodeId);
        if (! $periode) {
            return redirect()->route($this->routePrefix().'/jurnaling/months')
                ->withErrors(['error' => 'Invalid period selected.']);
        }

        $monthName = Carbon::createFromFormat('Y-m', $month)->translatedFormat('F');
        $year = Carbon::parse($periode->tanggal_awal)->year;
        $fileName = "jurnaling {$monthName} {$year}.xlsx";

        $exportClass = $this->jurnalingSheetClass();

        return Excel::download(new $exportClass($month, $periodeId), $fileName);
    }
}
